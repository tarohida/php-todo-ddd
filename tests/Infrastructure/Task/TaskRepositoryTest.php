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
use App\Domain\Task\TaskList;
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
        $task = new Task(1, 'title1');
        $repository = new TaskRepository($this->getPdoMockForUpdate());
        $repository->save($task);
    }

    public function test_method_getNextValFromSequence()
    {
        $data_set = [
            'nextval' => 1
        ];
        $pdo = $this->getPdoMockForFetch($data_set);
        $repository = new TaskRepository($pdo);
        self::assertSame(1, $repository->getNextValFromSequence());
    }

    private function getPdoMockForUpdate(): PDO|MockObject
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects(self::once())
            ->method('execute');
        $statement->expects(self::once())
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

    public function getPdoMock($statement): PDO|MockObject
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::once())
            ->method('prepare')
            ->willReturn($statement);
        return $pdo;
    }
}
