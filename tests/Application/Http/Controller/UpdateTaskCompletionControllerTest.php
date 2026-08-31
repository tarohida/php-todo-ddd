<?php
declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\UpdateTaskCompletionController;
use App\Application\Task\UpdateTaskCompletion;
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

final class UpdateTaskCompletionControllerTest extends TestCase
{
    public function test_returns_exact_updated_task_json(): void
    {
        $useCase = $this->createMock(UpdateTaskCompletion::class);
        $useCase->expects(self::once())->method('execute')->willReturn(
            new Task(new TaskId(1), new TaskTitle('task'), new TaskCompleted(true))
        );
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/tasks/1')
            ->withParsedBody(['completed' => true]);
        $response = (new UpdateTaskCompletionController($useCase))($request, new Response(), ['id' => '1']);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['task' => ['id' => 1, 'title' => 'task', 'completed' => true]],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR));
    }

    #[DataProvider('invalidBodies')]
    public function test_rejects_non_boolean_or_missing_completed(mixed $body): void
    {
        $useCase = $this->createMock(UpdateTaskCompletion::class);
        $useCase->expects(self::never())->method('execute');
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/tasks/1')->withParsedBody($body);
        $this->expectException(HttpBadRequestException::class);
        (new UpdateTaskCompletionController($useCase))($request, new Response(), ['id' => '1']);
    }

    public static function invalidBodies(): array
    {
        return [[null], [[]], [['completed' => 1]], [['completed' => 'true']], [['completed' => null]]];
    }

    public function test_maps_missing_task_to_not_found(): void
    {
        $useCase = $this->createStub(UpdateTaskCompletion::class);
        $useCase->method('execute')->willThrowException(new TaskNotFoundException());
        $request = (new ServerRequestFactory())->createServerRequest('PATCH', '/tasks/42')
            ->withParsedBody(['completed' => true]);
        $this->expectException(HttpNotFoundException::class);
        (new UpdateTaskCompletionController($useCase))($request, new Response(), ['id' => '42']);
    }
}
