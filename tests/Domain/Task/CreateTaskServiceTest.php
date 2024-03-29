<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\CreateTaskService;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;

class CreateTaskServiceTest extends TestCase
{
    private TaskTitle $title;
    private string $title_string;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title_string = 'title1';
        $this->title = new TaskTitle($this->title_string);
    }

    public function test_method_serve()
    {
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('save')
            ->with(
                self::callback(function($task) {
                    return $task->title() === $this->title_string;
                })
            );
        $task_id = new TaskId(1);
        $repository->method('createTaskId')
            ->willReturn($task_id);
        $service = new CreateTaskService($repository);
        $task = $service->serve($this->title);
        self::assertInstanceOf(Task::class, $task);
        self::assertSame(1, $task->id());
        self::assertSame($this->title_string, $task->title());
    }
}
