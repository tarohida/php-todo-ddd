<?php


namespace Tests\Application\Actions\Task;


use App\Application\Actions\Task\ListTasksAction;
use App\Application\Actions\Task\ListTasksActionInterface;
use App\Application\DTO\Task\TaskDataListInterface;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;
use PHPUnit\Framework\TestCase;

class ListTasksActionTest extends TestCase
{
    private TaskServiceInterface $task_service;
    private TaskRepositoryInterface $task_repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->task_repository = $this->createStub(TaskRepositoryInterface::class);
        $this->task_service = $this->createStub(TaskServiceInterface::class);
    }

    public function test_construct()
    {
        $this->assertInstanceOf(ListTasksAction::class, new ListTasksAction($this->task_repository, $this->task_service));
    }

    public function test_implements_ListTaskActionInterface()
    {
        $this->assertInstanceOf(ListTasksActionInterface::class, new ListTasksAction($this->task_repository, $this->task_service));
    }

    public function test_action()
    {
        $task_list = new TaskList([
            $this->createStub(TaskInterface::class),
            $this->createStub(TaskInterface::class)
        ]);
        $this->task_repository
            ->method('list')
            ->willReturn($task_list);

        $view_list_action = new ListTasksAction($this->task_repository, $this->task_service);
        $this->assertInstanceOf(TaskDataListInterface::class, $view_list_action->action());
        $this->assertCount(2, $view_list_action->action());
    }

    public function test_action_return_empty_task_data_list()
    {
        $task_list = new TaskList([]);
        $this->task_repository
            ->method('list')
            ->willReturn($task_list);

        $view_list_action = new ListTasksAction($this->task_repository, $this->task_service);
        $this->assertInstanceOf(TaskDataListInterface::class, $view_list_action->action());
        $this->assertCount(0, $view_list_action->action());
    }
}