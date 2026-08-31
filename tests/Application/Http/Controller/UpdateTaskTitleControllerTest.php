<?php
declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\UpdateTaskTitleController;
use App\Application\Task\UpdateTaskTitle;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

final class UpdateTaskTitleControllerTest extends TestCase
{
    public function test_returns_updated_task_json(): void
    {
        $useCase = $this->createMock(UpdateTaskTitle::class);
        $useCase->expects(self::once())->method('execute')->willReturn(
            new Task(new TaskId(4), new TaskTitle('after'), new TaskCompleted(true)),
        );
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/tasks/4/title')
            ->withParsedBody(['title' => 'after']);

        $response = (new UpdateTaskTitleController($useCase))($request, new Response(), ['id' => '4']);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['task' => ['id' => 4, 'title' => 'after', 'completed' => true]],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
    }

    #[DataProvider('invalidRequests')]
    public function test_rejects_invalid_input(mixed $id, mixed $body): void
    {
        $useCase = $this->createMock(UpdateTaskTitle::class);
        $useCase->expects(self::never())->method('execute');
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/tasks/x/title')->withParsedBody($body);

        $this->expectException(HttpBadRequestException::class);
        (new UpdateTaskTitleController($useCase))($request, new Response(), ['id' => $id]);
    }

    public static function invalidRequests(): array
    {
        return [
            ['x', ['title' => 'after']],
            ['1', null],
            ['1', []],
            ['1', ['title' => 1]],
            ['1', ['title' => '']],
            ['1', ['title' => str_repeat('a', 256)]],
        ];
    }

    public function test_translates_missing_task_to_not_found(): void
    {
        $useCase = $this->createStub(UpdateTaskTitle::class);
        $useCase->method('execute')->willThrowException(new TaskNotFoundException());
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/tasks/99/title')
            ->withParsedBody(['title' => 'after']);

        $this->expectException(HttpNotFoundException::class);
        (new UpdateTaskTitleController($useCase))($request, new Response(), ['id' => '99']);
    }
}
