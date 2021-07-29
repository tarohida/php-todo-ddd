<?php
declare(strict_types=1);

namespace Tests\Application\Action;

use App\Application\Action\TaskCreateAction;
use App\Application\Action\TaskCreateActionInterface;
use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\TaskRepositoryInterface;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = $this->createStub(TaskCreateCommandInterface::class);
        $this->repository = $this->createMock(TaskRepositoryInterface::class);
        $this->action = new TaskCreateAction($this->repository);
    }

    public function test_implements_TaskCreateActionInterface()
    {
        $this->assertInstanceOf(TaskCreateActionInterface::class, $this->action);
    }

    /**
     * @throws TaskValidateException
     */
    public function test_method_create()
    {
        $this->repository->expects($this->once())
            ->method('save');
        $this->repository->method('getNextValueInSequence')
            ->willReturn(1);
        $this->command->method('title')
            ->willReturn('title1');
        $action = new TaskCreateAction($this->repository);
        $action->create($this->command);
    }
}
