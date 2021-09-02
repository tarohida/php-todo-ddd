<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\ListTaskService;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ListTaskServiceTest extends TestCase
{
    public function test_method_list()
    {
        $repository = $this->getTaskRepositoryMock();
        $service = new ListTaskService($repository);
        $this->assertInstanceOf(ListTaskService::class, $service);
    }

    private function getTaskRepositoryMock()
    {
        return $this->createMock(TaskRepositoryInterface::class);
    }
}
