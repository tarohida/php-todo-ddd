<?php


namespace Tests\Infrastructure\Task;


use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepository;
use App\Infrastructure\Task\TaskDB;
use PDO;
use PHPUnit\Framework\TestCase;

class TaskDBTest extends TestCase
{
    private PDO $pdo;

    public function setUp(): void
    {
        parent::setUp();
        $db_host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $db_user = $_ENV['DB_USER'];
        $db_password = $_ENV['DB_PASSWORD'];

        $this->pdo = new PDO(
            "pgsql:host=${db_host};dbname=${db_name};",
            $db_user,
            $db_password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $this->empty();
        $this->seed();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->empty();
    }

    public function seed()
    {
        $query = <<< SQL
INSERT INTO tasks
VALUES (
        :id, :title
);
SQL;
        $id = 1;
        $title = 'title1';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        $id = 2;
        $title = 'title2';
        $stmt->execute();
    }

    /** @noinspection SqlWithoutWhere */
    public function empty()
    {
        $query = <<< SQL
DELETE from tasks 
SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

    }

    public function test_construct()
    {
        $this->assertInstanceOf(TaskDB::class, new TaskDB($this->pdo));
    }

    public function test_implement_TaskRepository()
    {
        $this->assertInstanceOf(TaskRepository::class, new TaskDB($this->pdo));
    }

    public function test_method_find()
    {
        $task_repository = new TaskDB($this->pdo);
        $task = $task_repository->find(1);
        $this->assertSame(1, $task->id());
    }

    public function test_method_find_return_null()
    {
        $task_repository = new TaskDB($this->pdo);
        $task = $task_repository->find(254);
        $this->assertSame(null, $task);
    }

    public function test_method_list()
    {
        $task_repository = new TaskDB($this->pdo);
        $task_list = $task_repository->list();
        $this->assertInstanceOf(TaskList::class, $task_list);
        $this->assertCount(2, $task_list);
    }

    public function test_method_list__return_empty_task_list()
    {
        $this->empty();
        $task_repository = new TaskDB($this->pdo);
        $task_list = $task_repository->list();
        $this->assertCount(0, $task_list);
    }
}