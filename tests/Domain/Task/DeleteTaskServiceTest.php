<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\DeleteTaskService;
use App\Domain\Task\DeleteTaskServiceInterface;
use App\Domain\Task\Exception\TaskValidateFailedWithIdException;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteTaskServiceTest
 * @package Tests\Domain\Task
 */
class DeleteTaskServiceTest extends TestCase
{
    private TaskRepositoryInterface $repository;
    private DeleteTaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(TaskRepositoryInterface::class);
        $this->service = new DeleteTaskService($this->repository);
    }

    public function test_implement_interface(): void
    {
        self::assertInstanceOf(DeleteTaskServiceInterface::class, $this->service);
    }

    /**
     * @throws TaskValidateFailedWithIdException
     */
    public function test_method_delete(): void
    {
        $this->repository->expects($this->once())
            ->method('delete');
        $service = new DeleteTaskService($this->repository);
        $service->delete(1);
    }
}
