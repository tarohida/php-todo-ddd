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
        $response = $this->getResponse();
        self::assertSame(200, $response->getStatusCode());
    }

    private function getResponse(): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request('GET', 'http://web/tasks');
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }
}
