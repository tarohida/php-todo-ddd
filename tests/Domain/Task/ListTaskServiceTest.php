<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\ListTaskService;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ListTaskServiceTest extends TestCase
{
    public function test_method_list()
    {
        $repository = $this->getTaskRepositoryMock();
        $service = new ListTaskService($repository);
        $this->assertInstanceOf(TaskList::class, $service->list());
    }

    private function getTaskRepositoryMock()
    {
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('list');
        return $repository;
    }
}
