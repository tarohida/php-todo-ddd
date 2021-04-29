<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use PHPUnit\Framework\TestCase;
use TypeError;

class TaskTest extends TestCase
{
    private Task $task;
    private int $id;
    private string $title;

    public function setUp(): void
    {
        $this->id = 1;
        $this->title = 'title1';
        $this->task = new Task($this->id, $this->title);
    }

    /**
     * @dataProvider invalid_construct_param_provider
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function test_construct_params_must_not_be_null($id, $title)
    {
        $this->expectException(TypeError::class);
        new Task($id, $title);
    }

    public function invalid_construct_param_provider(): array
    {
        // id, title
        return [
            [null, 'title1'],
            [1, null],
            [null, null]
        ];
    }

    public function test_implements_TaskInterface()
    {
        $this->assertInstanceOf(TaskInterface::class, $this->task);
    }

    public function test_method_id()
    {
        $this->assertSame($this->id, $this->task->id());
    }

    public function test_method_title()
    {
        $this->assertSame($this->title, $this->task->title());
    }
}
