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
        $form_params = [
            'title' => 'title1'
        ];
        $response = $this->requestPost($form_params);
        self::assertSame(200, $response->getStatusCode());
        $expected = <<<'JSON'
{"task":{"1":"title1"}}
JSON;
        self::assertSame($expected, (string)$response->getBody());
    }

    public function test_post_to_tasks_create_when_params_invalid_return_400()
    {
        $this->markTestIncomplete();
        $response = $this->requestPost('/tasks/create');
        self::assertSame(400, $response->getStatusCode());
    }

    private function getResponse(string $method, string $path, ): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request($method, 'http://web'.$path);
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }

    private function requestPost(array $form_params=[]): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request('POST', 'http://web'. '/tasks/create', [
                'form_params' => $form_params
            ]);
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }
}
