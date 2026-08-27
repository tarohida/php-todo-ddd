<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\CreateTaskController;

use App\Application\Task\CreateTask;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Exception\HttpBadRequestException;
use PHPUnit\Framework\Attributes\DataProvider;

class CreateTaskControllerTest extends TestCase
{
    public function test_method_invoke_call_service()
    {
        $service = $this->createMock(CreateTask::class);
        $request = $this->createStub(Request::class);
        $request->method('getParsedBody')
            ->willReturn(['title' => 'title1']);
        $response = new Response();
        $service->expects(self::once())
            ->method('execute');
        $controller = new CreateTaskController($service);
        call_user_func($controller, $request, $response, []);
    }

    public function test_returns_created_json_response(): void
    {
        $service = $this->createStub(CreateTask::class);
        $service->method('execute')->willReturn(
            new \App\Domain\Task\Task(
                \App\Domain\Task\TaskId::createFromMixedTypeValue(1),
                \App\Domain\Task\TaskTitle::createFromMixedTypeValue('Write tests')
            )
        );
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/tasks')
            ->withParsedBody(['title' => 'Write tests']);

        $response = (new CreateTaskController($service))($request, new Response(), []);

        self::assertSame(201, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['task' => ['id' => 1, 'title' => 'Write tests']],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR)
        );
    }

    #[DataProvider('invalidParsedBodies')]
    public function test_rejects_non_array_or_missing_parsed_body(mixed $body): void
    {
        $service = $this->createMock(CreateTask::class);
        $service->expects(self::never())->method('execute');
        $request = $this->createStub(Request::class);
        $request->method('getParsedBody')->willReturn($body);

        $this->expectException(HttpBadRequestException::class);
        (new CreateTaskController($service))($request, new Response(), []);
    }

    public static function invalidParsedBodies(): array
    {
        return [[null], ['title'], [1], [true]];
    }

    public function test_rejects_invalid_title_without_calling_use_case(): void
    {
        $service = $this->createMock(CreateTask::class);
        $service->expects(self::never())->method('execute');
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/tasks')
            ->withParsedBody(['title' => '']);

        $this->expectException(HttpBadRequestException::class);
        (new CreateTaskController($service))($request, new Response(), []);
    }
}
