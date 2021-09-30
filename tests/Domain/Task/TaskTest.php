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
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private int $id;
    private string $title;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->id = 1;
        $this->title = 'title1';
        $this->task = new Task(new TaskId($this->id), $this->title);
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
