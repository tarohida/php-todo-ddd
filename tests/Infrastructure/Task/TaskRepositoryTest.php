<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskTitle;
use App\Infrastructure\Task\TaskRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    public function test_method_list()
    {
        $data_set = [
            0 => [
                'id' => 1,
                'title' => 'title1'
            ],
            1 => [
                'id' => 2,
                'title' => 'title2'
            ],
            2 => [
                'id' => 3,
                'title' => 'title3'
            ]
        ];
        $pdo = $this->getPdoMockForFetch($data_set);
        $repository = new TaskRepository($pdo);
        $list = $repository->list();
        self::assertInstanceOf(TaskList::class, $list);
    }

    public function test_method_save()
    {
        $task = new Task(new TaskId(1), new TaskTitle('title1'));
        $repository = new TaskRepository($this->getPdoMockForUpdate());
        $repository->save($task);
    }

    public function test_method_getTaskIdFromSequence()
    {
        $data_set = [
            0 => [
                'nextval' => 1
            ]
        ];
        $pdo = $this->getPdoMockForFetch($data_set);
        $repository = new TaskRepository($pdo);
        $task_id = $repository->createTaskId();
        self::assertSame(1, $task_id->id());
    }

    public function test_method_delete_task()
    {
        $pdo = $this->getPdoMockForUpdate();
        $repository = new TaskRepository($pdo);
        $task_id = new TaskId(1);
        $repository->delete($task_id);
    }

    private function getPdoMockForUpdate(): PDO|MockObject
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects(self::once())
            ->method('execute');
        $statement->expects(self::atLeast(1))
            ->method('rowCount')
            ->willReturn(1);
        return $this->getPdoMock($statement);
    }

    private function getPdoMockForFetch(array $data_set): PDO|MockObject
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects(self::once())
            ->method('fetchAll')
            ->willReturn($data_set);
        $statement->expects(self::once())
            ->method('execute');
        return $this->getPdoMock($statement);
    }

    private function getPdoMock($statement): PDO|MockObject
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::once())
            ->method('prepare')
            ->willReturn($statement);
        return $pdo;
    }
}
