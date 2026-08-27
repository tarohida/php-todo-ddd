<?php
declare(strict_types=1);

namespace Tests\Application\Task;

use App\Application\Task\DeleteTask;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryTaskRepository;

final class DeleteTaskTest extends TestCase
{
    public function test_deletes_an_existing_task(): void
    {
        $repository = new InMemoryTaskRepository(
            new Task(new TaskId(3), new TaskTitle('Remove me')),
        );

        (new DeleteTask($repository))->execute(new TaskId(3));

        self::assertCount(0, $repository->list());
    }

    public function test_reports_a_missing_task(): void
    {
        $this->expectException(TaskNotFoundException::class);

        (new DeleteTask(new InMemoryTaskRepository()))->execute(new TaskId(99));
    }
}
