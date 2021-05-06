<?php
declare(strict_types=1);

namespace Tests\Application\Action;

use App\Application\Action\Exception\TaskAlreadyExistsException\TaskAlreadyExistsException;
use App\Application\Action\TaskCreateAction;
use App\Application\Action\TaskCreateActionInterface;
use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskCreateActionTest
 * @package Tests\Application\Action
 */
class TaskCreateActionTest extends TestCase
{
    private TaskCreateAction $action;
    private TaskCreateCommandInterface|Stub $command;
    private TaskRepositoryInterface|MockObject $repository;
    private TaskServiceInterface|Stub $task_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = $this->createStub(TaskCreateCommandInterface::class);
        $this->repository = $this->createMock(TaskRepositoryInterface::class);
        $this->task_service = $this->createStub(TaskServiceInterface::class);
        $this->action = new TaskCreateAction($this->repository, $this->task_service);
    }

    public function test_implements_TaskCreateActionInterface()
    {
        $this->assertInstanceOf(TaskCreateActionInterface::class, $this->action);
    }

    /**
     * @throws TaskAlreadyExistsException
     */
    public function test_method_create()
    {
        $this->task_service->method('taskExists')
            ->willReturn(false);
        $this->repository->expects($this->once())
            ->method('save');
        $this->command->method('id')
            ->willReturn(1);
        $this->command->method('title')
            ->willReturn('title1');
        $action = new TaskCreateAction($this->repository, $this->task_service);
        $action->create($this->command);
    }

    public function test_method_create_throw_TaskAlreadyExistsException()
    {
        $this->task_service->method('taskExists')
            ->willReturn(true);
        $action = new TaskCreateAction($this->repository, $this->task_service);
        $this->command->method('id')
            ->willReturn(1);
        try {
            $action->create($this->command);
        } catch (TaskAlreadyExistsException $ex) {
            $this->assertSame(1, $ex->getTaskId());
        }
    }

}
