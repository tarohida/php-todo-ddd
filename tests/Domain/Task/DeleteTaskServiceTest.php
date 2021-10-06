<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\DeleteTaskService;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DeleteTaskServiceTest extends TestCase
{
    public function test_method_serve()
    {
        $id = 1;
        $task_id = new TaskId($id);
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(function (TaskId $task_id) use ($id) {
                return $task_id->id() === $id;
            }));
        $service = new DeleteTaskService($repository);
        $service->serve($task_id);
    }

}
