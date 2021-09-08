<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Domain\Task\TaskList;
use App\Infrastructure\Task\TaskRepository;

use PDO;
use PDOStatement;
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
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects(self::once())
            ->method('fetchAll')
            ->willReturn($data_set);
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::once())
            ->method('prepare')
            ->willReturn($statement);
        $repository = new TaskRepository($pdo);
        $list = $repository->list();
        self::assertInstanceOf(TaskList::class, $list);
    }
}
