<?php


namespace Tests\Application\DTO\Task;


use App\Application\DTO\Task\TaskData;
use App\Application\DTO\Task\TaskDataList;
use Iterator;
use PHPUnit\Framework\TestCase;
use TypeError;

class TaskDataListTest extends TestCase
{
    private array $task_data_list;

    public function setUp(): void
    {
        $this->task_data_list = [
            $this->createStub(TaskData::class),
            $this->createStub(TaskData::class)
        ];
    }

    public function test_construct()
    {
        $task_data_list = new TaskDataList($this->task_data_list);
        $this->assertInstanceOf(TaskDataList::class, $task_data_list);
    }

    public function test_construct_throw_TypeError_if_argument_array_value_is_not_TaskData()
    {
        $this->expectException(TypeError::class);
        $task_data_list = [
            $this->createStub(TaskData::class),
            []
        ];
        new TaskDataList($task_data_list);
    }

    public function test_implements_iterator()
    {
        $this->assertInstanceOf(Iterator::class, new TaskDataList($this->task_data_list));
    }

    public function test_foreach()
    {
        $task_data_list = new TaskDataList($this->task_data_list);

        foreach ($task_data_list as $task_data) {
            $this->assertInstanceOf(TaskData::class, $task_data);
        }
    }

    public function test_method_isEmpty()
    {
        $task_data_list = new TaskDataList([]);
        $this->assertTrue($task_data_list->isEmpty());

    }
}