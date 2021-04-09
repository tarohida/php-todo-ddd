<?php


namespace Tests\Application\Actions\Task;


use App\Application\Actions\Task\ViewTaskAction;
use App\Application\Actions\Task\ViewTaskActionInterface;
use App\Application\DTO\Task\TaskDataInterface;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;
use App\Exeption\Application\Actions\Task\SpecifiedTaskNotFoundException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class ViewTaskActionTest extends TestCase
{
    /**
     * @var TaskRepositoryInterface|Stub
     */
    private $task_repository;
    /**
     * @var TaskServiceInterface|Stub
     */
    private $task_service;

    public function setUp(): void
    {
        $this->task_repository = $this->createStub(TaskRepositoryInterface::class);
        $this->task_service = $this->createStub(TaskServiceInterface::class);
    }
    public function test_construct()
    {
        $this->assertInstanceOf(
            ViewTaskAction::class,
            new ViewTaskAction($this->task_repository, $this->task_service)
        );
    }

    public function test_implements_ViewTaskActionInterface()
    {
        $this->assertInstanceOf(ViewTaskActionInterface::class, new ViewTaskAction($this->task_repository, $this->task_service));
    }

    public function test_method_action()
    {
        $task = new Task(1, 'title');
        $this->task_repository->method('find')
            ->willReturn($task);
        $task_action = new ViewTaskAction($this->task_repository, $this->task_service);
        $id = 1;
        $this->assertInstanceOf(TaskDataInterface::class, $task_action->action($id));
    }

    public function test_method_action_throws_SpecifiedTaskNotFoundException()
    {
        $this->task_repository->method('find')
            ->willReturn(null);
        try {
            $task_action = new ViewTaskAction($this->task_repository, $this->task_service);
            $id = 1;
            $task_action->action($id);
        } catch (SpecifiedTaskNotFoundException $e) {
            $this->assertSame(1, $e->getTaskId());
        }
    }
}