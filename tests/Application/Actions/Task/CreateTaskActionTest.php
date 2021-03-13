<?php
declare(strict_types=1);


namespace Tests\Application\Actions\Task;


use App\Application\Actions\Task\CreateTaskAction;
use App\Application\Actions\Task\CreateTaskActionInterface;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use App\Domain\Task\TaskServiceInterface;
use PHPUnit\Framework\TestCase;

class CreateTaskActionTest extends TestCase
{
    /**
     * @var TaskRepositoryInterface
     */
    private $task_repository;
    /**
     * @var TaskServiceInterface
     */
    private $task_service;

    public function setUp(): void
    {
        $this->task_repository = $this->createMock(TaskRepositoryInterface::class);
        $this->task_service = $this->createStub(TaskService::class);
    }

    public function test_construct()
    {
        $action = new CreateTaskAction($this->task_repository, $this->task_service);
        $this->assertInstanceOf(CreateTaskAction::class, $action);
    }

    public function test_implements_CreateTaskActionInterface()
    {
        $action = new CreateTaskAction($this->task_repository, $this->task_service);
        $this->assertInstanceOf(CreateTaskActionInterface::class, $action);
    }

    public function test_method_action()
    {
        $id = 1;
        $title = 'title1';
        $task = new Task($id, $title);
        $this->task_repository
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($task));
        $action = new CreateTaskAction($this->task_repository, $this->task_service);
        $action->action($task);
    }
}