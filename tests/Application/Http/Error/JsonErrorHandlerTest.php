<?php
declare(strict_types=1);

namespace Tests\Application\Http\Error;

use App\Application\Http\Error\JsonErrorHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Throwable;

final class JsonErrorHandlerTest extends TestCase
{
    #[DataProvider('errors')]
    public function test_preserves_the_stable_json_error_contract(Throwable $exception, int $status, string $message): void
    {
        $response = (new JsonErrorHandler(new ResponseFactory()))(
            (new ServerRequestFactory())->createServerRequest('GET', '/missing'),
            $exception,
            false,
            false,
            false,
        );

        self::assertSame($status, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['error' => ['status' => $status, 'message' => $message]],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
    }

    public static function errors(): array
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/missing');

        return [
            [new HttpBadRequestException($request), 400, 'Bad Request'],
            [new HttpNotFoundException($request), 404, 'Not Found'],
            [new HttpMethodNotAllowedException($request), 405, 'Method Not Allowed'],
            [new RuntimeException('password=do-not-expose'), 500, 'Internal Server Error'],
        ];
    }
}
