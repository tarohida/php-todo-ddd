<?php


namespace Tests\Application\DTO\Task;


use App\Application\DTO\Task\TaskData;
use App\Domain\Task\Task;
use PHPUnit\Framework\TestCase;

class TaskDataTest extends TestCase
{
    private int $id;
    private string $title;
    /**
     * @var TaskData
     */
    private TaskData $task_data;

    public function setUp(): void
    {
        $this->id = 1;
        $this->title = 'title';
        $task = new Task($this->id, $this->title);
        $this->task_data = new TaskData($task);

    }
    public function test_construct()
    {
        $this->assertInstanceOf(TaskData::class, $this->task_data);
    }

    public function test_method_id()
    {
        $this->assertSame($this->id, $this->task_data->id());
    }

    public function test_method_title()
    {
        $this->assertSame($this->title, $this->task_data->title());
    }

}