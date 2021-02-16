<?php


namespace App\Infrastructure\Task;


use App\Domain\Task\Task;

use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepository;
use App\Infrastructure\Task\Exception\DuplicatedTaskException;
use UnexpectedValueException;
use PDO;

class TaskDB implements TaskRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param int $task_id
     * @return Task|null
     * @throws DuplicatedTaskException
     * @throws UnexpectedValueException
     */
    public function find(int $task_id): ?Task
    {
        $query = <<< SQL
SELECT * from tasks
where id = :task_id;
SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':task_id', $task_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (empty($result)) {
            return null;
        }
        if (count($result) >= 2) {
            throw new DuplicatedTaskException("task_id: $task_id");
        }
        if (isset($result[0]['id']) && isset($result[0]['title'])) {
            return new Task($result[0]['id'], $result[0]['title']);
        }
        throw new UnexpectedValueException('PDO response format is unexpected.');
    }

    public function list(): TaskList
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
}