<?php


namespace Tests\Application\Actions\Task;


use App\Application\Actions\ActionInterface;
use App\Application\Actions\Task\ListTasksAction;
use App\Application\DTO\Task\TaskDataList;
use App\Domain\Task\Task;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use PHPUnit\Framework\TestCase;

class ListTasksActionTest extends TestCase
{
    private TaskService $task_service;
    private TaskRepositoryInterface $task_repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->task_repository = $this->createStub(TaskRepositoryInterface::class);
        $this->task_service = $this->createStub(TaskService::class);
    }

    public function test_construct()
    {
        $this->assertInstanceOf(ListTasksAction::class, new ListTasksAction($this->task_repository, $this->task_service));
    }

    public function test_implements_ActionInterface()
    {
        $this->assertInstanceOf(ActionInterface::class, new ListTasksAction($this->task_repository, $this->task_service));
    }

    public function test_action()
    {
        $task_list = new TaskList([
            $this->createStub(Task::class),
            $this->createStub(Task::class)
        ]);
        $this->task_repository
            ->method('list')
            ->willReturn($task_list);

        $view_list_action = new ListTasksAction($this->task_repository, $this->task_service);
        $this->assertInstanceOf(TaskDataList::class, $view_list_action->action());
        $this->assertCount(2, $view_list_action->action());
    }

    public function test_action_return_empty_task_data_list()
    {
        $task_list = new TaskList([]);
        $this->task_repository
            ->method('list')
            ->willReturn($task_list);

        $view_list_action = new ListTasksAction($this->task_repository, $this->task_service);
        $this->assertInstanceOf(TaskDataList::class, $view_list_action->action());
        $this->assertCount(0, $view_list_action->action());
    }
}