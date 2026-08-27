<?php
declare(strict_types=1);

namespace Tests\Application\Task;

use App\Application\Task\CreateTask;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryTaskRepository;

final class CreateTaskTest extends TestCase
{
    public function test_creates_and_persists_a_task(): void
    {
        $repository = new InMemoryTaskRepository();

        $task = (new CreateTask($repository))->execute(new TaskTitle('Write tests'));

        self::assertSame(1, $task->id());
        self::assertSame('Write tests', $task->title());
        self::assertSame([$task], iterator_to_array($repository->list()));
    }
}
