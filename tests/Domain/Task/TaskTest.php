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

use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use App\Domain\Task\TaskCompleted;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function test_new_task_is_incomplete_by_default(): void
    {
        $task = new Task(new TaskId(1), new TaskTitle('task'));
        self::assertFalse($task->completed());
    }

    public function test_completion_can_be_changed_explicitly(): void
    {
        $task = new Task(new TaskId(1), new TaskTitle('task'), new TaskCompleted(false));
        $updated = $task->withCompletion(new TaskCompleted(true));

        self::assertFalse($task->completed());
        self::assertTrue($updated->completed());
    }
    private int $id;
    private string $title;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->id = 1;
        $this->title = 'title1';
        $this->task = new Task(new TaskId($this->id), new TaskTitle($this->title));
    }

    public function test_method_id()
    {
        self::assertSame($this->id, $this->task->id());
    }

    public function test_method_title()
    {
        self::assertSame($this->title, $this->task->title());
    }
}
