<?php
declare(strict_types=1);

namespace App\Application\Http\Error;

use App\Application\Http\Middleware\RequestLogContext;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

final class JsonErrorHandler implements ErrorHandlerInterface
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
    ): ResponseInterface {
        $status = $exception instanceof HttpException ? $exception->getCode() : 500;
        $requestContext = $request->getAttribute(RequestLogContext::REQUEST_ATTRIBUTE);
        if ($status >= 500 && $requestContext instanceof RequestLogContext) {
            $requestContext->recordException($exception);
        }

        $messages = [
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
        ];
        $body = json_encode([
            'error' => [
                'status' => $status,
                'message' => $messages[$status] ?? 'Internal Server Error',
            ],
        ], JSON_THROW_ON_ERROR);
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write($body);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
