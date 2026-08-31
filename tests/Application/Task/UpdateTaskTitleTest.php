<?php
declare(strict_types=1);

namespace Tests\Application\Task;

use App\Application\Task\UpdateTaskTitle;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryTaskRepository;

final class UpdateTaskTitleTest extends TestCase
{
    public function test_updates_and_returns_the_task(): void
    {
        $repository = new InMemoryTaskRepository(
            new Task(new TaskId(3), new TaskTitle('before'), new TaskCompleted(true)),
        );

        $task = (new UpdateTaskTitle($repository))->execute(new TaskId(3), new TaskTitle('after'));

        self::assertSame(3, $task->id());
        self::assertSame('after', $task->title());
        self::assertTrue($task->completed());
        self::assertSame('after', $repository->list()->current()->title());
    }

    public function test_reports_a_missing_task(): void
    {
        $this->expectException(TaskNotFoundException::class);
        (new UpdateTaskTitle(new InMemoryTaskRepository()))->execute(new TaskId(99), new TaskTitle('after'));
    }
}
