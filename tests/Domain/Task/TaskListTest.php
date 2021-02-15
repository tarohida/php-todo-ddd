<?php


namespace Tests\Domain\Task;


use App\Domain\Task\Task;
use App\Domain\Task\TaskList;
use Iterator;
use PHPUnit\Framework\TestCase;
use TypeError;

class TaskListTest extends TestCase
{
    private array $task_array;

    public function setUp(): void
    {
        $this->task_array = [
            $this->createStub(Task::class),
            $this->createStub(Task::class)
        ];
    }
    public function test_construct()
    {
        $this->assertInstanceOf(TaskList::class, new TaskList($this->task_array));
    }

    public function test_construct_throw_TypeError_when_array_value_is_not_Task()
    {
        $wrong_task_array = [
            $this->createStub(Task::class),
            1
        ];
        $this->expectException(TypeError::class);
        new TaskList($wrong_task_array);
    }

    public function test_implements_iterator()
    {
        $this->assertInstanceOf(Iterator::class, new TaskList($this->task_array));
    }

    public function test_foreach()
    {
        $task_list = new TaskList($this->task_array);
        foreach ($task_list as $task) {
            $this->assertInstanceOf(Task::class, $task);
        }
    }

}