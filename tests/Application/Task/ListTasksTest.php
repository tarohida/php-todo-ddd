<?php
declare(strict_types=1);

namespace Tests\Application\Task;

use App\Application\Task\ListTasks;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryTaskRepository;

final class ListTasksTest extends TestCase
{
    public function test_lists_persisted_tasks(): void
    {
        $task = new Task(new TaskId(7), new TaskTitle('Existing task'));
        $repository = new InMemoryTaskRepository($task);

        $tasks = (new ListTasks($repository))->execute();

        self::assertSame([$task], iterator_to_array($tasks));
    }
}
