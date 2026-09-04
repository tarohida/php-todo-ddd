<?php
declare(strict_types=1);

namespace App\Application\Http\Middleware;

use App\Application\Http\Error\JsonErrorHandler;
use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use UnexpectedValueException;

final class RequestLoggingMiddleware implements MiddlewareInterface
{
    public const REQUEST_ID_HEADER = 'X-Request-Id';

    private const SAFE_REQUEST_ID_PATTERN = '/\A[A-Za-z0-9][A-Za-z0-9._-]{0,63}\z/D';

    private static int $fallbackSequence = 0;

    private readonly Closure $requestIdGenerator;

    private readonly Closure $clock;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly JsonErrorHandler $jsonErrorHandler,
        ?callable $requestIdGenerator = null,
        ?callable $clock = null,
    ) {
        $this->requestIdGenerator = Closure::fromCallable(
            $requestIdGenerator ?? static fn (): string => bin2hex(random_bytes(16)),
        );
        $this->clock = Closure::fromCallable($clock ?? static fn (): float => microtime(true));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startedAt = $this->currentTime();
        try {
            $requestId = $this->requestId($request);
        } catch (Throwable $exception) {
            $requestId = $this->fallbackRequestId();
            $context = new RequestLogContext($requestId);
            $request = $request->withAttribute(RequestLogContext::REQUEST_ATTRIBUTE, $context);
            $response = ($this->jsonErrorHandler)($request, $exception, false, false, false);
            $this->logCompletionSafely($request, $response->getStatusCode(), $context, $startedAt);

            return $response->withHeader(self::REQUEST_ID_HEADER, $requestId);
        }

        $context = new RequestLogContext($requestId);
        $request = $request->withAttribute(RequestLogContext::REQUEST_ATTRIBUTE, $context);

        try {
            $response = $handler->handle($request);
        } catch (Throwable $exception) {
            $response = ($this->jsonErrorHandler)($request, $exception, false, false, false);
        }

        $this->logCompletionSafely($request, $response->getStatusCode(), $context, $startedAt);

        return $response->withHeader(self::REQUEST_ID_HEADER, $requestId);
    }

    private function requestId(ServerRequestInterface $request): string
    {
        $requestIds = $request->getHeader(self::REQUEST_ID_HEADER);
        if (count($requestIds) === 1 && preg_match(self::SAFE_REQUEST_ID_PATTERN, $requestIds[0]) === 1) {
            return $requestIds[0];
        }

        $requestId = ($this->requestIdGenerator)();
        if (!is_string($requestId) || preg_match(self::SAFE_REQUEST_ID_PATTERN, $requestId) !== 1) {
            throw new UnexpectedValueException('Request ID generation failed');
        }

        return $requestId;
    }

    private function logCompletionSafely(
        ServerRequestInterface $request,
        int $status,
        RequestLogContext $requestContext,
        ?float $startedAt,
    ): void {
        $finishedAt = $this->currentTime();
        $duration = $startedAt === null || $finishedAt === null
            ? 0.0
            : max(0.0, round(($finishedAt - $startedAt) * 1000, 3));
        $context = [
            'request_id' => $requestContext->requestId(),
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath() ?: '/',
            'status' => $status,
            'duration_ms' => $duration,
        ];
        if ($status >= 500 && $requestContext->exceptionClass() !== null) {
            $context['exception_class'] = $requestContext->exceptionClass();
        }

        try {
            if ($status >= 500) {
                $this->logger->error('HTTP request completed', $context);
                return;
            }

            $this->logger->info('HTTP request completed', $context);
        } catch (Throwable) {
            // Logging must never alter the HTTP response or trigger recursive logging.
        }
    }

    private function currentTime(): ?float
    {
        try {
            return (float) ($this->clock)();
        } catch (Throwable) {
            return null;
        }
    }

    private function fallbackRequestId(): string
    {
        ++self::$fallbackSequence;
        $timestamp = hrtime(true);

        return sprintf('fallback-%x-%x', is_int($timestamp) ? $timestamp : 0, self::$fallbackSequence);
    }
}
