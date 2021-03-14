<?php


namespace Tests\Infrastructure\Task;


use App\Domain\Task\Task;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Task\TaskRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
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
        $this->delete_all_tasks();
        $this->seed();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->delete_all_tasks();
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
    public function delete_all_tasks()
    {
        $query = <<< SQL
DELETE from tasks 
SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

    }

    public function test_construct()
    {
        $this->assertInstanceOf(TaskRepository::class, new TaskRepository($this->pdo));
    }

    public function test_implement_TaskRepository()
    {
        $this->assertInstanceOf(TaskRepositoryInterface::class, new TaskRepository($this->pdo));
    }

    public function test_method_find()
    {
        $task_repository = new TaskRepository($this->pdo);
        $task = $task_repository->find(1);
        $this->assertSame(1, $task->id());
    }

    public function test_method_find_return_null()
    {
        $task_repository = new TaskRepository($this->pdo);
        $task = $task_repository->find(254);
        $this->assertSame(null, $task);
    }

    public function test_method_list()
    {
        $task_repository = new TaskRepository($this->pdo);
        $task_list = $task_repository->list();
        $this->assertInstanceOf(TaskList::class, $task_list);
        $this->assertCount(2, $task_list);
    }

    public function test_method_list__return_empty_task_list()
    {
        $this->delete_all_tasks();
        $task_repository = new TaskRepository($this->pdo);
        $task_list = $task_repository->list();
        $this->assertCount(0, $task_list);
    }

    public function test_method_save()
    {
        $task = new Task(10, 'title1');
        $task_repository = new TaskRepository($this->pdo);
        $task_repository->save($task);

        $this->assertInstanceOf(Task::class, $task_repository->find(10));
    }

    public function test_method_delete()
    {
        $task_id = 1;
        $task_repository = new TaskRepository($this->pdo);
        $task_repository->delete($task_id);

        $this->assertNull($task_repository->find(1));
    }
}