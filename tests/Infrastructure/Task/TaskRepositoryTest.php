<?php
/** @noinspection NonAsciiCharacters */
declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskIteratorInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Pdo\Exception\NotAffectedException;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedValueException;
use App\Infrastructure\Pdo\Exception\TooAffectedException;
use App\Infrastructure\Task\TaskRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Stub;
use Tests\Infrastructure\DatabaseTestCase;

/**
 * Class TaskRepositoryTest
 * @package Tests\Infrastructure\Task
 */
class TaskRepositoryTest extends DatabaseTestCase
{
    private PDO $pdo;
    private TaskRepository $repository;
    private PDOStatement|Stub $pdo_statement;
    private Stub|PDO $pdo_mock;
    private TaskInterface|Stub $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = $this->getPdo();
        $this->pdo_mock = $this->createStub(PDO::class);
        $this->repository = new TaskRepository($this->pdo);
        $this->pdo_statement = $this->createStub(PDOStatement::class);
        $this->task = $this->createStub(TaskInterface::class);
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
        $id = 2;
        $title = 'title2';
        $pdo_statement->execute();
    }

    private function clean()
    {
        $query = <<<SQL
delete from tasks
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->execute();
    }

    public function test_implements_TaskRepositoryInterface()
    {
        $this->assertInstanceOf(TaskRepositoryInterface::class, $this->repository);
    }

    /**
     * @throws SpecifiedTaskNotFoundException|TaskValidateException
     */
    public function test_method_find()
    {
        $task = $this->repository->find(1);
        $this->assertInstanceOf(TaskInterface::class, $task);
        $this->assertSame(1, $task->id());
        $this->assertSame('title1', $task->title());
    }

    /**
     * @throws TaskValidateException
     */
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
Array
(
    [0] => 1
)

EOF;

        $this->pdo_statement->method('fetchAll')
            ->willReturn($array);
        $this->pdo_mock->method('prepare')
            ->willReturn($this->pdo_statement);
        $repository = new TaskRepository($this->pdo_mock);
        try {
            $repository->find(1);
        } catch (PdoReturnUnexpectedValueException $ex) {
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
Array
(
    [0] => 1
)

EOF;

        $this->pdo_statement->method('fetchAll')
            ->willReturn($array);
        $this->pdo_mock->method('prepare')
            ->willReturn($this->pdo_statement);
        $repository = new TaskRepository($this->pdo_mock);
        try {
            $repository->find(1);
        } catch (PdoReturnUnexpectedValueException $ex) {
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
Array
(
    [0] => 1
)

EOF;

        $this->pdo_statement->method('fetchAll')
            ->willReturn($array);
        $this->pdo_mock->method('prepare')
            ->willReturn($this->pdo_statement);
        $repository = new TaskRepository($this->pdo_mock);
        try {
            $repository->find(1);
        } catch (PdoReturnUnexpectedValueException $ex) {
            $this->assertSame($logging_message, $ex->getLoggingMessage());
        }
    }

    /**
     * @throws TaskValidateException
     * @throws SpecifiedTaskNotFoundException
     */
    public function test_method_save()
    {
        $this->clean();
        $this->task = $this->createStub(TaskInterface::class);
        $this->task->method('id')
            ->willReturn(1);
        $this->task->method('title')
            ->willReturn('title1');
        $this->repository->save($this->task);
        $ret = $this->repository->find(1);
        $this->assertSame(1, $ret->id());
    }

    public function test_method_save_if_affected_row_equal_to_0_throw_NotAffectedException()
    {
        $this->task->method('id')
            ->willReturn(1);
        $this->task->method('title')
            ->willReturn('title1');
        $this->pdo_statement->method('rowCount')
            ->willReturn(0);
        $this->pdo_mock->method('prepare')
            ->willReturn($this->pdo_statement);
        $repository = new TaskRepository($this->pdo_mock);
        try {
            $repository->save($this->task);
            $this->fail('please raise exception');
        } catch (NotAffectedException $ex) {
            $this->assertTrue(!empty($ex->getLoggingMessage()));
        }
    }

    public function test_method_save_if_affected_row_not_1_throwTooAffectedException()
    {
        $this->task->method('id')
            ->willReturn(1);
        $this->task->method('title')
            ->willReturn('title1');
        $this->pdo_statement->method('rowCount')
            ->willReturn(3);
        $this->pdo_mock->method('prepare')
            ->willReturn($this->pdo_statement);
        $repository = new TaskRepository($this->pdo_mock);
        try {
            $repository->save($this->task);
            $this->fail('please raise exception');
        } catch (TooAffectedException $ex) {
            $this->assertTrue(!empty($ex->getLoggingMessage()));
        }
    }

    public function test_method_list()
    {
        $list = $this->repository->list();
        $this->assertInstanceOf(TaskIteratorInterface::class, $list);
        $this->assertCount(2, $list);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->pdo);
    }

    /**
     * sequenceによって変える値は変わるので、単にintが変えることのみテストする
     */
    public function test_method_getNextValueInSequense()
    {
        $this->assertIsInt($this->repository->getNextValueInSequence());
    }
}
