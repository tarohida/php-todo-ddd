<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace HttpTests\Tasks;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class GetTasksTest extends TestCase
{
    public function test_get()
    {
        $response = $this->getResponse();
        self::assertSame(404, $response->getStatusCode());
    }

    private function getResponse()
    {
        $client = new Client();
        try {
            return $client->request('GET', 'http://web/tasks');
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }
}
