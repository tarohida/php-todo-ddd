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
use PDO;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\Attributes\DataProvider;

class TasksApiContractTest extends TestCase
{
    /** @var list<int> */
    private array $createdTaskIds = [];

    private function baseUrl(): string
    {
        return rtrim($_ENV['HTTP_TEST_BASE_URL'] ?? 'http://web', '/');
    }

    protected function tearDown(): void
    {
        $client = new Client(['http_errors' => false]);
        foreach ($this->createdTaskIds as $id) {
            $client->delete($this->baseUrl() . '/tasks/' . $id);
        }
    }

    public function test_get()
    {
        $response = $this->requestGet();
        self::assertSame(200, $response->getStatusCode());
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $tasks = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($tasks);
        foreach ($tasks as $task) {
            self::assertIsBool($task['completed']);
        }
    }

    public function test_post_json_to_canonical_tasks_endpoint(): void
    {
        $client = new Client(['http_errors' => false]);
        $response = $client->post($this->baseUrl() . '/tasks', [
            'headers' => ['Accept' => 'application/json'],
            'json' => ['title' => 'canonical task'],
        ]);

        self::assertSame(201, $response->getStatusCode());
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $body = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('canonical task', $body['task']['title']);
        self::assertFalse($body['task']['completed']);
        self::assertIsInt($body['task']['id']);
        $this->createdTaskIds[] = $body['task']['id'];
    }

    public function test_invalid_json_input_returns_json_bad_request(): void
    {
        $client = new Client(['http_errors' => false]);
        $response = $client->post($this->baseUrl() . '/tasks', [
            'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            'body' => '{"title":""}',
        ]);

        self::assertSame(400, $response->getStatusCode());
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $body = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error', $body);
    }

    #[DataProvider('invalidJsonBodies')]
    public function test_non_object_or_malformed_json_returns_stable_bad_request(string $json): void
    {
        $response = (new Client(['http_errors' => false]))->post($this->baseUrl() . '/tasks', [
            'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            'body' => $json,
        ]);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(
            ['error' => ['status' => 400, 'message' => 'Bad Request']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR)
        );
    }

    public static function invalidJsonBodies(): array
    {
        return [['"title"'], ['1'], ['null'], ['{"title":']];
    }

    #[DataProvider('invalidRouteIds')]
    public function test_delete_rejects_non_canonical_route_ids(string $id): void
    {
        $response = (new Client(['http_errors' => false]))->delete($this->baseUrl() . '/tasks/' . rawurlencode($id));
        self::assertSame(400, $response->getStatusCode());
    }

    public static function invalidRouteIds(): array
    {
        return [['1.5'], ['1e0'], [' 1'], ['1 '], ['0'], ['01'], ['-1'], [(string) PHP_INT_MAX . '0']];
    }

    public function test_deleting_missing_task_returns_json_not_found(): void
    {
        $client = new Client(['http_errors' => false]);
        $response = $client->delete($this->baseUrl() . '/tasks/2147483647', [
            'headers' => ['Accept' => 'application/json'],
        ]);

        self::assertSame(404, $response->getStatusCode());
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $body = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error', $body);
    }

    #[Group('exclusive-database')]
    public function test_unexpected_persistence_failure_returns_generic_json_server_error(): void
    {
        $pdo = new PDO(
            sprintf('pgsql:host=%s;port=5432;dbname=%s', $_ENV['DB_HOST'], $_ENV['DB_NAME']),
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
        );
        $invalidTaskId = null;
        $inserted = false;

        try {
            $invalidTaskId = (int) $pdo->query("select nextval('tasks_id_seq')")->fetchColumn();
            $statement = $pdo->prepare('insert into tasks (id, title) values (:id, :title)');
            $statement->execute(['id' => $invalidTaskId, 'title' => '']);
            $inserted = $statement->rowCount() === 1;

            $response = (new Client(['http_errors' => false]))->get($this->baseUrl() . '/tasks');

            self::assertSame(500, $response->getStatusCode());
            self::assertSame(
                ['error' => ['status' => 500, 'message' => 'Internal Server Error']],
                json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
            );
            self::assertStringNotContainsString('PdoReturnUnexpectedResultException', (string) $response->getBody());
        } finally {
            if ($inserted) {
                $delete = $pdo->prepare('delete from tasks where id = :id');
                $delete->execute(['id' => $invalidTaskId]);
            }
        }
    }

    public function test_cors_preflight_returns_configured_headers(): void
    {
        $client = new Client(['http_errors' => false]);
        $allowedOrigin = $_ENV['ALLOW_ORIGIN_URL'];
        $response = $client->request('OPTIONS', $this->baseUrl() . '/tasks', [
            'headers' => ['Origin' => $allowedOrigin],
        ]);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($allowedOrigin, $response->getHeaderLine('Access-Control-Allow-Origin'));
        self::assertStringContainsString('POST', $response->getHeaderLine('Access-Control-Allow-Methods'));
        self::assertSame('GET, POST, PATCH, DELETE, OPTIONS', $response->getHeaderLine('Access-Control-Allow-Methods'));
        self::assertSame('Content-Type, Accept, Origin', $response->getHeaderLine('Access-Control-Allow-Headers'));
        self::assertSame('Origin', $response->getHeaderLine('Vary'));
    }

    public function test_cors_does_not_allow_an_unconfigured_origin(): void
    {
        $response = (new Client(['http_errors' => false]))->request('OPTIONS', $this->baseUrl() . '/tasks', [
            'headers' => ['Origin' => 'https://untrusted.example'],
        ]);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('', $response->getHeaderLine('Access-Control-Allow-Origin'));
        self::assertSame('', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }

    public function test_post_to_tasks_create()
    {
        $form_params = [
            'title' => 'title1'
        ];
        $response = $this->requestPost($form_params);
        self::assertSame(201, $response->getStatusCode());
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $body = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('title1', $body['task']['title']);
        self::assertIsInt($body['task']['id']);
        $this->createdTaskIds[] = $body['task']['id'];
    }

    public function test_post_to_tasks_create_when_params_invalid_return_400()
    {
        $response = $this->requestPost();
        self::assertSame(400, $response->getStatusCode());
        $response = $this->requestPost(['title' => '']);
        self::assertSame(400, $response->getStatusCode());
    }

    public function test_delete_to_tasks_id()
    {
        $created = json_decode(
            (string) $this->requestPost(['title' => 'delete target'])->getBody(),
            true,
            flags: JSON_THROW_ON_ERROR
        );
        $this->createdTaskIds[] = $created['task']['id'];
        $response = $this->requestDelete($created['task']['id']);
        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', (string) $response->getBody());
        self::assertSame('', $response->getHeaderLine('Content-Type'));
        $this->createdTaskIds = array_values(array_diff($this->createdTaskIds, [$created['task']['id']]));
    }

    public function test_patch_updates_completion_and_is_idempotent(): void
    {
        $created = json_decode((string) $this->requestPost(['title' => 'complete target'])->getBody(), true, flags: JSON_THROW_ON_ERROR);
        $id = $created['task']['id'];
        $this->createdTaskIds[] = $id;
        $client = new Client(['http_errors' => false]);
        foreach ([true, true, false] as $completed) {
            $response = $client->patch($this->baseUrl() . '/tasks/' . $id, ['json' => ['completed' => $completed]]);
            self::assertSame(200, $response->getStatusCode());
            self::assertSame(['task' => ['id' => $id, 'title' => 'complete target', 'completed' => $completed]],
                json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR));
        }
    }

    #[DataProvider('invalidCompletionBodies')]
    public function test_patch_rejects_non_boolean_completion(mixed $completed): void
    {
        $response = (new Client(['http_errors' => false]))->patch($this->baseUrl() . '/tasks/1', ['json' => ['completed' => $completed]]);
        self::assertSame(400, $response->getStatusCode());
    }

    public static function invalidCompletionBodies(): array
    {
        return [[1], [0], ['true'], [null]];
    }

    public function test_patch_missing_task_returns_json_not_found(): void
    {
        $response = (new Client(['http_errors' => false]))->patch($this->baseUrl() . '/tasks/2147483647', [
            'json' => ['completed' => true],
        ]);
        self::assertSame(404, $response->getStatusCode());
        self::assertSame(['error' => ['status' => 404, 'message' => 'Not Found']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR));
    }

    private function requestGet(): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request('GET', $this->baseUrl() . '/tasks');
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }

    private function requestPost(array $form_params=[]): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request('POST', $this->baseUrl() . '/tasks/create', [
                'form_params' => $form_params
            ]);
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }

    private function requestDelete(int $id): ResponseInterface
    {
        $client = new Client();
        try {
            return $client->request('DELETE', $this->baseUrl() . '/tasks/' . $id);
        } catch (ClientException $e) {
            return $e->getResponse();
        }
    }
}
