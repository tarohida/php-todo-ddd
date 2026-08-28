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
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Exception\TaskAlreadyExistsException;
use App\Domain\Task\Exception\TaskTitleValidateException;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedResultException;

use PDO;
use PDOException;
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

    public function test_list_translates_an_invalid_database_row_to_an_infrastructure_exception(): void
    {
        $repository = new TaskRepository($this->getPdoMockForFetch([
            ['id' => 1, 'title' => null],
        ]));

        try {
            $repository->list();
            self::fail('An invalid database row must not cross the repository boundary.');
        } catch (PdoReturnUnexpectedResultException $exception) {
            self::assertInstanceOf(TaskTitleValidateException::class, $exception->getPrevious());
        }
    }

    public function test_method_save()
    {
        $task = new Task(new TaskId(1), new TaskTitle('title1'));
        $repository = new TaskRepository($this->getPdoMockForUpdate(2));
        $repository->save($task);
    }

    public function test_save_translates_a_zero_row_primary_key_conflict(): void
    {
        $repository = new TaskRepository($this->getPdoMockForUpdate(2, 0));

        $this->expectException(TaskAlreadyExistsException::class);
        $repository->save(new Task(new TaskId(1), new TaskTitle('title1')));
    }

    public function test_save_propagates_an_unrelated_database_failure(): void
    {
        $pdoException = new PDOException('duplicate title');
        $pdoException->errorInfo = ['23505', 7, 'duplicate key violates constraint "tasks_title_key"'];
        $repository = new TaskRepository($this->getPdoMockForSaveException($pdoException));

        $this->expectExceptionObject($pdoException);
        $repository->save(new Task(new TaskId(1), new TaskTitle('title1')));
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

    public function test_delete_missing_task_throws_not_found(): void
    {
        $statement = $this->createStub(PDOStatement::class);
        $statement->method('rowCount')->willReturn(0);
        $pdo = $this->createStub(PDO::class);
        $pdo->method('prepare')->willReturn($statement);
        $repository = new TaskRepository($pdo);

        $this->expectException(TaskNotFoundException::class);
        $repository->delete(new TaskId(2147483647));
    }

    private function getPdoMockForUpdate(int $statementCount = 1, int $rowCount = 1): PDO|MockObject
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects(self::exactly($statementCount))
            ->method('execute');
        $statement->expects(self::atLeast(1))
            ->method('rowCount')
            ->willReturn($rowCount);
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::exactly($statementCount))
            ->method('prepare')
            ->willReturn($statement);
        return $pdo;
    }

    private function getPdoMockForSaveException(PDOException $exception): PDO|MockObject
    {
        $sequenceStatement = $this->createStub(PDOStatement::class);
        $saveStatement = $this->createStub(PDOStatement::class);
        $saveStatement->method('execute')->willThrowException($exception);
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($sequenceStatement, $saveStatement);
        return $pdo;
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
