<?php


namespace Tests\Domain\Task;


use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private int $id;
    private string $title;
    /**
     * @var TaskInterface
     */
    private TaskInterface $task;

    public function setUp(): void
    {
        $this->id = 1;
        $this->title = 'new task';
        $this->task = new Task($this->id, $this->title);
    }

    public function test_construct()
    {
        $this->assertInstanceOf(Task::class, $this->task);
    }

    public function test_property_id_get()
    {
        $this->assertSame($this->id, $this->task->id());
    }

    public function test_property_title_get()
    {
        $this->assertSame($this->title, $this->task->title());
    }
}