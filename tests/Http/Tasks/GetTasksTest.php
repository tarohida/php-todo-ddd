<?php 
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */
/** @noinspection HttpUrlsUsage */

declare(strict_types=1);

namespace Tests\Http\Tasks;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class GetTasksTest extends TestCase
{
    public function test_get()
    {
        $response = $this->getResponse('GET', '/tasks');
        self::assertSame(200, $response->getStatusCode());
    }

    public function test_post_to_tasks_create()
    {
        $response = $this->getResponse('POST', '/tasks/create');
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('Hello! post', (string)$response->getBody());
    }

    private function getResponse(string $method, string $path): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request($method, 'http://web'.$path);
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }
}
