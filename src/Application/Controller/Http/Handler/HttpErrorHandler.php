<?php
declare(strict_types=1);

namespace App\Application\Controller\Http\Handler;

use App\Application\Controller\Http\Api\Response\Rfc7807ResponseInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;

/**
 * Class HttpErrorHandler
 * @package App\Application\Controller\Http\Handler
 */
class HttpErrorHandler extends ErrorHandler
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        if ($exception instanceof HttpException) {
            $title = $exception->getTitle();
            $status_code = $exception->getCode();
            $description = $exception->getDescription();
            $rfc7807_formatted = [
                'type' => null,
                'title' => $title,
                'detail' => $description,
                'instance' => null,
            ];

            $payload = json_encode($rfc7807_formatted, JSON_PRETTY_PRINT);
            $response = $this->responseFactory->createResponse($status_code);
            $response->getBody()->write($payload);
            return $response;
        }

        if ($exception instanceof Rfc7807ResponseInterface) {
            $type = $exception->getType();
            $status_code = $exception->getStatus();
            $title = $exception->getTitle();
            $detail = $exception->getDetail();
            $extension = $exception->getExtensions();
            $rfc7807_formatted = [
                'type' => $type,
                'title' => $title,
                'detail' => $detail,
                'instance' => null,
            ];
            $rfc7807_formatted = array_merge($rfc7807_formatted, $extension);
            $payload = json_encode($rfc7807_formatted, JSON_PRETTY_PRINT);
            $response = $this->responseFactory->createResponse($status_code);
            $response->getBody()->write($payload);
            return $response;
        }
        $rfc7807_formatted = [
            'type' => 'https://github.com/tarohida/php-todo-ddd/wiki/errors#application-error',
            'title' => 'application-error',
            'detail' => 'application-error',
            'instance' => null,
            'contact' => 'sk8trou@gmail.com'
        ];
        $payload = json_encode($rfc7807_formatted, JSON_PRETTY_PRINT);
        $response = $this->responseFactory->createResponse(500);
        $response->getBody()->write($payload);
        return $response;
    }
}
