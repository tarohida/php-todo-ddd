<?php


namespace Tests\Application\Actions\Task;


use App\Application\Actions\Task\ViewTaskAction;
use App\Application\DTO\Task\TaskData;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskService;
use PHPUnit\Framework\MockObject\Stub;
use App\Application\Exception\HttpNotFoundException;
use PHPUnit\Framework\TestCase;

class ViewTaskActionTest extends TestCase
{
    /**
     * @var TaskRepository|Stub
     */
    private $task_repository;
    /**
     * @var TaskService|Stub
     */
    private $task_service;

    public function setUp(): void
    {
        $this->task_repository = $this->createStub(TaskRepository::class);
        $this->task_service = $this->createStub(TaskService::class);
    }
    public function test_construct()
    {
        $this->assertInstanceOf(
            ViewTaskAction::class,
            new ViewTaskAction($this->task_repository, $this->task_service)
        );
    }

    public function test_method_action()
    {
        $task = new Task(1, 'title');
        $this->task_repository->method('find')
            ->willReturn($task);
        $task_action = new ViewTaskAction($this->task_repository, $this->task_service);
        $id = 1;
        $this->assertInstanceOf(TaskData::class, $task_action->action($id));
    }

    public function test_method_action_throws_HttpNotFoundException()
    {
        $this->task_repository->method('find')
            ->willReturn(null);
        $this->expectException(HttpNotFoundException::class);
        $task_action = new ViewTaskAction($this->task_repository, $this->task_service);
        $id = 1;
        $task_action->action($id);
    }
}