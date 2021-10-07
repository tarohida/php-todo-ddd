<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedResultException;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function list(): TaskList
    {
        $sql = <<< SQL
select id, title
from tasks
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $data_set = $statement->fetchAll(PDO::FETCH_ASSOC);
        $tasks = [];
        foreach ($data_set as $data) {
             try {
                 $tasks[] = Task::createFromPdoDataSet($data);
             } catch (TaskValidateException $e) {
                 throw new PdoReturnUnexpectedResultException(previous: $e, data_set:$data_set);
             }
        }
        return new TaskList($tasks);
    }

    public function save(Task $task): void
    {
        $query = <<<SQL
insert into tasks
(id, title) values
(:id, :title)
SQL;
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $task->id());
        $statement->bindValue(':title', $task->title());
        $statement->execute();
        if ($statement->rowCount() !== 1) {
            throw new PdoReturnUnexpectedResultException(data_set: [$statement->rowCount()]);
        }
    }

    public function createTaskId(): TaskId
    {
        $query = <<<'SQL'
select nextval('tasks_id_seq')
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->execute();
        $data_set = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
        try {
            return TaskId::createFromPdoResultRows($data_set);
        } catch (TaskIdValidateException) {
            throw new PdoReturnUnexpectedResultException(data_set: $data_set);
        }
    }

    public function delete(TaskId $id): void
    {
        $query = <<< 'SQL'
delete from tasks
where id = :id
SQL;
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id->id());
        $statement->execute();
        if ($statement->rowCount() !== 1 && $statement->rowCount() !== 0) {
            throw new PdoReturnUnexpectedResultException(data_set: [$statement->rowCount()]);
        }
    }
}