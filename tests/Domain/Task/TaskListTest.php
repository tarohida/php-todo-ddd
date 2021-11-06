<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskList;
use PHPUnit\Framework\TestCase;

class TaskListTest extends TestCase
{
    private TaskList $task_list;

    protected function setUp(): void
    {
        parent::setUp();
        $tasks = [
            $this->createStub(Task::class),
            $this->createStub(Task::class),
            $this->createStub(Task::class)
        ];
        $this->task_list = new TaskList($tasks);
    }

    public function test_foreach()
    {
        self::assertIsIterable($this->task_list);
        foreach ($this->task_list as $task) {
            $this->assertInstanceOf(Task::class, $task);
        }
    }

}
