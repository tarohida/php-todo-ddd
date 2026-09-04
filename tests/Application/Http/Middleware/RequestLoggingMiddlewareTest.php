<?php
declare(strict_types=1);

namespace Tests\Application\Http\Middleware;

use App\Application\Http\Error\JsonErrorHandler;
use App\Application\Http\Middleware\RequestLoggingMiddleware;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use PDOException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\AbstractLogger;
use RuntimeException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Stringable;

final class RequestLoggingMiddlewareTest extends TestCase
{
    #[DataProvider('safeClientRequestIds')]
    public function test_accepts_a_safe_bounded_client_request_id(string $requestId): void
    {
        $testHandler = new TestHandler();
        $middleware = $this->middleware($testHandler, 'generated-request-id');
        $request = $this->request('GET', '/health')->withHeader('X-Request-Id', $requestId);

        $response = $middleware->process($request, $this->responseHandler(200));

        self::assertSame($requestId, $response->getHeaderLine('X-Request-Id'));
        self::assertSame($requestId, $testHandler->getRecords()[0]->context['request_id']);
    }

    public static function safeClientRequestIds(): array
    {
        return [
            ['a'],
            ['client-ABC_123.456'],
            [str_repeat('x', 64)],
        ];
    }

    #[DataProvider('unsafeClientRequestIds')]
    public function test_replaces_an_unsafe_client_request_id(string $requestId): void
    {
        $testHandler = new TestHandler();
        $middleware = $this->middleware($testHandler, 'generated-request-id');
        $request = $this->request('GET', '/health')->withHeader('X-Request-Id', $requestId);

        $response = $middleware->process($request, $this->responseHandler(200));

        self::assertSame('generated-request-id', $response->getHeaderLine('X-Request-Id'));
        self::assertSame('generated-request-id', $testHandler->getRecords()[0]->context['request_id']);
    }

    public static function unsafeClientRequestIds(): array
    {
        return [
            [''],
            ['contains space'],
            ['-starts-with-symbol'],
            ['comma,separated'],
            ['non-ascii-あ'],
            [str_repeat('x', 65)],
        ];
    }

    public function test_replaces_multiple_client_request_id_values(): void
    {
        $testHandler = new TestHandler();
        $middleware = $this->middleware($testHandler, 'generated-request-id');
        $request = $this->request('GET', '/health')->withHeader('X-Request-Id', ['first', 'second']);

        $response = $middleware->process($request, $this->responseHandler(200));

        self::assertSame('generated-request-id', $response->getHeaderLine('X-Request-Id'));
    }

    public function test_default_generated_request_id_is_safe_and_bounded(): void
    {
        $testHandler = new TestHandler();
        $clock = $this->clock(20.0, 20.001);
        $middleware = new RequestLoggingMiddleware(
            new Logger('test', [$testHandler]),
            new JsonErrorHandler(new ResponseFactory()),
            clock: $clock,
        );

        $response = $middleware->process($this->request('GET', '/health'), $this->responseHandler(200));
        $requestId = $response->getHeaderLine('X-Request-Id');

        self::assertMatchesRegularExpression('/\A[A-Za-z0-9][A-Za-z0-9._-]{0,63}\z/D', $requestId);
        self::assertSame($requestId, $testHandler->getRecords()[0]->context['request_id']);
    }

    #[DataProvider('responseStatuses')]
    public function test_adds_the_request_id_and_logs_completion_for_every_response(
        int $status,
        Level $expectedLevel,
    ): void {
        $testHandler = new TestHandler();
        $middleware = $this->middleware($testHandler, 'server-generated-id', 10.0, 10.125);
        $request = $this->request('POST', '/tasks?ignored=query-secret');

        $response = $middleware->process($request, $this->responseHandler($status));

        self::assertSame('server-generated-id', $response->getHeaderLine('X-Request-Id'));
        $records = $testHandler->getRecords();
        self::assertCount(1, $records);
        self::assertSame($expectedLevel, $records[0]->level);
        self::assertSame('HTTP request completed', $records[0]->message);
        self::assertSame([
            'request_id' => 'server-generated-id',
            'method' => 'POST',
            'path' => '/tasks',
            'status' => $status,
            'duration_ms' => 125.0,
        ], $records[0]->context);
    }

    public static function responseStatuses(): array
    {
        return [
            'normal response' => [200, Level::Info],
            'client error' => [404, Level::Info],
            'server error' => [500, Level::Error],
        ];
    }

    public function test_logs_only_the_exception_class_for_a_server_error_and_keeps_the_generic_contract(): void
    {
        $testHandler = new TestHandler();
        $middleware = $this->middleware($testHandler, 'unused-generated-id');
        $request = $this->request('POST', '/tasks?password=query-secret')
            ->withHeader('X-Request-Id', 'client-request-id')
            ->withHeader('Authorization', 'Bearer header-secret');
        $request->getBody()->write('{"password":"body-secret"}');
        $errorHandler = new JsonErrorHandler(new ResponseFactory());
        $next = new class ($errorHandler) implements RequestHandlerInterface {
            public function __construct(private readonly JsonErrorHandler $errorHandler)
            {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return ($this->errorHandler)(
                    $request,
                    new PDOException('password=db-secret host=private-db'),
                    false,
                    false,
                    false,
                );
            }
        };

        $response = $middleware->process($request, $next);

        self::assertSame(500, $response->getStatusCode());
        self::assertSame('client-request-id', $response->getHeaderLine('X-Request-Id'));
        self::assertSame(
            ['error' => ['status' => 500, 'message' => 'Internal Server Error']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
        $record = $testHandler->getRecords()[0];
        self::assertSame(PDOException::class, $record->context['exception_class']);
        self::assertSame([
            'request_id',
            'method',
            'path',
            'status',
            'duration_ms',
            'exception_class',
        ], array_keys($record->context));
        $serializedRecord = json_encode($record->toArray(), JSON_THROW_ON_ERROR);
        foreach (['query-secret', 'header-secret', 'body-secret', 'db-secret', 'private-db', 'password'] as $secret) {
            self::assertStringNotContainsString($secret, $serializedRecord);
            self::assertStringNotContainsString($secret, (string) $response->getBody());
        }
    }

    public function test_a_logger_write_failure_never_replaces_a_successful_response(): void
    {
        $logger = new class extends AbstractLogger {
            public int $calls = 0;

            public function log($level, string|Stringable $message, array $context = []): void
            {
                ++$this->calls;
                throw new RuntimeException('logger-write-secret');
            }
        };
        $middleware = new RequestLoggingMiddleware(
            $logger,
            new JsonErrorHandler(new ResponseFactory()),
            static fn (): string => 'generated-request-id',
            $this->clock(10.0, 10.001),
        );

        $response = $middleware->process($this->request('GET', '/health'), $this->responseHandler(200));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('generated-request-id', $response->getHeaderLine('X-Request-Id'));
        self::assertSame('', (string) $response->getBody());
        self::assertSame(1, $logger->calls);
    }

    public function test_an_exception_outside_slim_error_middleware_returns_one_safe_generic_500_record(): void
    {
        $testHandler = new TestHandler();
        $middleware = $this->middleware($testHandler, 'generated-request-id');
        $next = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new RuntimeException('password=handler-secret host=private-db');
            }
        };

        $response = $middleware->process(
            $this->request('GET', '/outside-error')->withHeader('X-Request-Id', 'outside-error-request'),
            $next,
        );

        self::assertSame(500, $response->getStatusCode());
        self::assertSame('outside-error-request', $response->getHeaderLine('X-Request-Id'));
        self::assertSame(
            ['error' => ['status' => 500, 'message' => 'Internal Server Error']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
        $records = $testHandler->getRecords();
        self::assertCount(1, $records);
        self::assertSame(RuntimeException::class, $records[0]->context['exception_class']);
        self::assertStringNotContainsString('handler-secret', json_encode($records[0]->toArray(), JSON_THROW_ON_ERROR));
        self::assertStringNotContainsString('private-db', (string) $response->getBody());
    }

    public function test_request_id_and_logger_failures_still_return_a_safe_generic_500_without_recursion(): void
    {
        $logger = new class extends AbstractLogger {
            public int $calls = 0;

            /** @var array<string, mixed> */
            public array $lastContext = [];

            public function log($level, string|Stringable $message, array $context = []): void
            {
                ++$this->calls;
                $this->lastContext = $context;
                throw new RuntimeException('logger-open-secret');
            }
        };
        $middleware = new RequestLoggingMiddleware(
            $logger,
            new JsonErrorHandler(new ResponseFactory()),
            static fn (): string => throw new RuntimeException('request-id-generator-secret'),
            $this->clock(10.0, 10.001),
        );

        $response = $middleware->process($this->request('GET', '/health'), $this->responseHandler(200));
        $requestId = $response->getHeaderLine('X-Request-Id');

        self::assertSame(500, $response->getStatusCode());
        self::assertMatchesRegularExpression('/\A[A-Za-z0-9][A-Za-z0-9._-]{0,63}\z/D', $requestId);
        self::assertSame(
            ['error' => ['status' => 500, 'message' => 'Internal Server Error']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
        self::assertSame(1, $logger->calls);
        self::assertSame($requestId, $logger->lastContext['request_id']);
        self::assertSame(RuntimeException::class, $logger->lastContext['exception_class']);
        $serialized = json_encode($logger->lastContext, JSON_THROW_ON_ERROR) . (string) $response->getBody();
        self::assertStringNotContainsString('request-id-generator-secret', $serialized);
        self::assertStringNotContainsString('logger-open-secret', $serialized);
    }

    public function test_actual_slim_error_stack_returns_one_correlated_500_record(): void
    {
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);
        $responseFactory = new ResponseFactory();
        $jsonErrorHandler = new JsonErrorHandler($responseFactory);
        $app = AppFactory::create($responseFactory);
        $app->get('/boom', static function (): never {
            throw new PDOException('password=slim-stack-secret host=private-db');
        });
        $errorMiddleware = $app->addErrorMiddleware(false, false, false);
        $errorMiddleware->setDefaultErrorHandler($jsonErrorHandler);
        $allowedOrigin = 'https://frontend.example';
        $app->add(static function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($allowedOrigin): ResponseInterface {
            $response = $handler->handle($request);
            if ($request->getHeaderLine('Origin') !== $allowedOrigin) {
                return $response;
            }

            return $response
                ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
                ->withHeader('Access-Control-Expose-Headers', 'X-Request-Id');
        });
        $app->add(new RequestLoggingMiddleware(
            $logger,
            $jsonErrorHandler,
            static fn (): string => 'unused-generated-id',
            $this->clock(10.0, 10.001),
        ));

        $response = $app->handle(
            $this->request('GET', '/boom')
                ->withHeader('Origin', $allowedOrigin)
                ->withHeader('X-Request-Id', 'slim-stack-request'),
        );

        self::assertSame(500, $response->getStatusCode());
        self::assertSame('slim-stack-request', $response->getHeaderLine('X-Request-Id'));
        self::assertSame($allowedOrigin, $response->getHeaderLine('Access-Control-Allow-Origin'));
        self::assertSame('X-Request-Id', $response->getHeaderLine('Access-Control-Expose-Headers'));
        self::assertSame(
            ['error' => ['status' => 500, 'message' => 'Internal Server Error']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
        $records = $testHandler->getRecords();
        self::assertCount(1, $records);
        self::assertSame(PDOException::class, $records[0]->context['exception_class']);
        $serialized = json_encode($records[0]->toArray(), JSON_THROW_ON_ERROR) . (string) $response->getBody();
        self::assertStringNotContainsString('slim-stack-secret', $serialized);
        self::assertStringNotContainsString('private-db', $serialized);
    }

    private function middleware(
        TestHandler $testHandler,
        string $generatedRequestId,
        float $startedAt = 10.0,
        float $finishedAt = 10.001,
    ): RequestLoggingMiddleware {
        return new RequestLoggingMiddleware(
            new Logger('test', [$testHandler]),
            new JsonErrorHandler(new ResponseFactory()),
            static fn (): string => $generatedRequestId,
            $this->clock($startedAt, $finishedAt),
        );
    }

    private function clock(float ...$values): callable
    {
        return static function () use (&$values): float {
            $value = array_shift($values);
            self::assertNotNull($value);
            return $value;
        };
    }

    private function request(string $method, string $uri): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $uri);
    }

    private function responseHandler(int $status): RequestHandlerInterface
    {
        return new class ($status) implements RequestHandlerInterface {
            public function __construct(private readonly int $status)
            {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response($this->status);
            }
        };
    }
}
