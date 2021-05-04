<?php
/** @noinspection NonAsciiCharacters */
declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Task\TaskRepository;
use PDO;
use PDOStatement;
use Tests\Infrastructure\DatabaseTestCase;
use Tests\Infrastructure\Exception\RuntimeException\PdoReturnUnexpectedValueException;

class TaskRepositoryTest extends DatabaseTestCase
{
    private PDO $pdo;
    private TaskRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdo = $this->getPdo();
        $this->repository = new TaskRepository($this->pdo);
        $this->clean();
        $this->seed();
    }

    public function seed()
    {
        $sql = <<<SQL
insert into tasks
(id, title)
values (:id, :title)
SQL;
        $id = 1;
        $title = 'title1';
        $pdo_statement = $this->pdo->prepare($sql);
        $pdo_statement->bindParam(':id', $id);
        $pdo_statement->bindParam(':title', $title);
        $pdo_statement->execute();
    }

    private function clean()
    {
        $query = <<<SQL
delete from tasks
where id = :id;
SQL;
        $id = 1;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->bindParam(':id', $id);
        $pdo_statement->execute();
    }

    public function test_implements_TaskRepositoryInterface()
    {
        $this->assertInstanceOf(TaskRepositoryInterface::class, $this->repository);
    }

    /**
     * @throws SpecifiedTaskNotFoundException
     */
    public function test_method_find()
    {
        $task = $this->repository->find(1);
        $this->assertInstanceOf(TaskInterface::class, $task);
        $this->assertSame(1, $task->id());
        $this->assertSame('title1', $task->title());
    }

    public function test_method_find_throw_SpecifiedTaskNotFoundException()
    {
        try {
            $this->repository->find(255);
        } catch (SpecifiedTaskNotFoundException $ex) {
            $this->assertSame(255, $ex->getSpecifiedId());
        }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function test_method_find_throw_PdoReturnUnexpectedValueException_1行以上の行が返った()
    {
        $array = [
            ['id' => 1, 'title' => 'title1'],
            ['id' => 1, 'title' => 'title2']
        ];
        $logging_message = <<<EOF
Array
(
    [0] => Array
        (
            [id] => 1
            [title] => title1
        )

    [1] => Array
        (
            [id] => 1
            [title] => title2
        )

)

EOF;

        $pdo_statement = $this->createStub(PDOStatement::class);
        $pdo_statement->method('fetchAll')
            ->willReturn($array);
        $pdo = $this->createStub(PDO::class);
        $pdo->method('prepare')
            ->willReturn($pdo_statement);
        $repository = new TaskRepository($pdo);
        try {
            $repository->find(1);
        } catch (PdoReturnUnexpectedValueException $ex) {
            $this->assertSame('1行以上の行が返されました', $ex->getMessage());
            $this->assertSame($array, $ex->getParams());
            $this->assertSame($logging_message, $ex->getLoggingMessage());
        }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function test_method_find_throw_PdoReturnUnexpectedValueException_パラメタが見つからなかった()
    {
        $array = [
            ['id' => 1, 'hatsunemiku' => 'title1'],
        ];
        $logging_message = <<<EOF
Array
(
    [0] => Array
        (
            [id] => 1
            [hatsunemiku] => title1
        )

)

EOF;

        $pdo_statement = $this->createStub(PDOStatement::class);
        $pdo_statement->method('fetchAll')
            ->willReturn($array);
        $pdo = $this->createStub(PDO::class);
        $pdo->method('prepare')
            ->willReturn($pdo_statement);
        $repository = new TaskRepository($pdo);
        try {
            $repository->find(1);
        } catch (PdoReturnUnexpectedValueException $ex) {
            $this->assertSame('必要なパラメタが存在しません', $ex->getMessage());
            $this->assertSame($array, $ex->getParams());
            $this->assertSame($logging_message, $ex->getLoggingMessage());
        }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function test_method_find_throw_PdoReturnUnexpectedValueException_idの値が不正()
    {
        $array = [
            ['id' => 'string_string', 'title' => 'title1'],
        ];
        $logging_message = <<<EOF
Array
(
    [0] => Array
        (
            [id] => string_string
            [title] => title1
        )

)

EOF;

        $pdo_statement = $this->createStub(PDOStatement::class);
        $pdo_statement->method('fetchAll')
            ->willReturn($array);
        $pdo = $this->createStub(PDO::class);
        $pdo->method('prepare')
            ->willReturn($pdo_statement);
        $repository = new TaskRepository($pdo);
        try {
            $repository->find(1);
        } catch (PdoReturnUnexpectedValueException $ex) {
            $this->assertSame('idの値が不正です', $ex->getMessage());
            $this->assertSame($array, $ex->getParams());
            $this->assertSame($logging_message, $ex->getLoggingMessage());
        }
    }
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->pdo);
    }

}
