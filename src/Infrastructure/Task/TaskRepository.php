<?php


namespace App\Infrastructure\Task;


use App\Domain\Task\Task;

use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskListInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Exeption\Infrastructure\Task\DuplicatedTaskException;
use UnexpectedValueException;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param int $task_id
     * @return TaskInterface|null
     * @throws DuplicatedTaskException
     * @throws UnexpectedValueException
     */
    public function find(int $task_id): ?TaskInterface
    {
        $query = <<< SQL
SELECT * from tasks
where id = :task_id
SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':task_id', $task_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return null;
        }
        if (count($result) >= 2) {
            // Task must not be duplicated.
            throw new DuplicatedTaskException("task_id: $task_id");
        }
        if (isset($result[0]['id']) && isset($result[0]['title'])) {
            return new Task($result[0]['id'], $result[0]['title']);
        }
        throw new UnexpectedValueException('PDO response format is unexpected.');
    }

    public function list(): TaskListInterface
    {
        $query = <<< SQL
SELECT * from tasks
SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $task_array = [];
        foreach ($result as $task) {
            if (!isset($task['id']) || !isset($task['title'])) {
                throw new UnexpectedValueException('PDO response format is unexpected.');
            }
            $task_array[] = new Task($task['id'], $task['title']);
        }
        return new TaskList($task_array);
    }

    public function save(TaskInterface $task): void
    {
        $query = <<< SQL
INSERT INTO tasks (id, title) VALUES (:id, :title)
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $id = $task->id();
        $title = $task->title();
        $pdo_statement->bindParam(':id', $id, PDO::PARAM_INT);
        $pdo_statement->bindParam('title', $title, PDO::PARAM_STR);
        $pdo_statement->execute();
    }

    public function delete(int $task_id): void
    {
        $query = <<< SQL
DELETE from tasks
where id = :id
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->bindParam(':id', $task_id, PDO::PARAM_INT);
        $pdo_statement->execute();

    }
}