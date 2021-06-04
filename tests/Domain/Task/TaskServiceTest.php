<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use App\Domain\Task\TaskServiceInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskServiceTest
 * @package Tests\Domain\Task
 */
class TaskServiceTest extends TestCase
{
    private TaskRepositoryInterface|Stub $repository;
    private TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createStub(TaskRepositoryInterface::class);
        $this->service = new TaskService($this->repository);
    }

    public function test_implements_TaskServiceInterface()
    {
        $this->assertInstanceOf(TaskServiceInterface::class, $this->service);
    }

    public function test_method_exists_return_true()
    {
        $id = 1;
        $service = new TaskService($this->repository);
        $this->assertSame(true, $service->taskExists($id));
    }

    public function test_method_exists_return_false()
    {
        $id = 1;
        $ex = new SpecifiedTaskNotFoundException($id);
        $this->repository->method('find')
            ->willThrowException($ex);
        $service = new TaskService($this->repository);
        $this->assertSame(false, $service->taskExists($id));
    }
}
