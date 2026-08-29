<?php
declare(strict_types=1);

namespace Tests\Application\Task;

use App\Application\Task\UpdateTaskCompletion;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryTaskRepository;

final class UpdateTaskCompletionTest extends TestCase
{
    public function test_it_updates_completion_and_preserves_identity_and_title(): void
    {
        $repository = new InMemoryTaskRepository(new Task(new TaskId(1), new TaskTitle('task')));
        $updated = (new UpdateTaskCompletion($repository))->execute(new TaskId(1), new TaskCompleted(true));
        self::assertSame(1, $updated->id());
        self::assertSame('task', $updated->title());
        self::assertTrue($updated->completed());
    }

    public function test_missing_task_is_not_created(): void
    {
        $repository = new InMemoryTaskRepository();
        $this->expectException(TaskNotFoundException::class);
        (new UpdateTaskCompletion($repository))->execute(new TaskId(1), new TaskCompleted(true));
    }
}
