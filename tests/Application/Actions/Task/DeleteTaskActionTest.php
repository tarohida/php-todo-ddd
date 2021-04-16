<?php
declare(strict_types=1);

namespace Tests\Application\Actions\Task;

use App\Application\Actions\Task\DeleteTaskAction;
use App\Application\Actions\Task\DeleteTaskActionInterface;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DeleteTaskActionTest extends TestCase
{
    /**
     * @var TaskRepositoryInterface
     */
    private TaskRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(TaskRepositoryInterface::class);
    }

    public function test_construct()
    {
        $action = new DeleteTaskAction($this->repository);
        $this->assertInstanceOf(DeleteTaskAction::class, $action);
    }

    public function test_implements_DeleteTaskActionInterface()
    {
        $action = new DeleteTaskAction($this->repository);
        $this->assertInstanceOf(DeleteTaskActionInterface::class, $action);
    }

    public function test_method_action()
    {
        $task_id = 1;
        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo($task_id));
        $action = new DeleteTaskAction($this->repository);
        $action->action($task_id);
    }
}
