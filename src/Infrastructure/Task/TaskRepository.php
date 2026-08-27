<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

use App\Domain\Task\Exception\TaskAlreadyExistsException;
use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedResultException;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{
    private TaskRowMapper $rowMapper;

    public function __construct(
        private PDO $pdo,
        ?TaskRowMapper $rowMapper = null,
    ) {
        $this->rowMapper = $rowMapper ?? new TaskRowMapper();
    }

    public function list(): TaskList
    {
        $sql = <<< SQL
select id, title
from tasks
order by id
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $data_set = $statement->fetchAll(PDO::FETCH_ASSOC);
        $tasks = [];
        foreach ($data_set as $data) {
             try {
                 $tasks[] = $this->rowMapper->map($data);
             } catch (TaskValidateException $e) {
                 throw new PdoReturnUnexpectedResultException(previous: $e, data_set:$data_set);
             }
        }
        return new TaskList($tasks);
    }

    public function save(Task $task): void
    {
        $this->advanceSequencePast($task->id());
        $query = <<<SQL
insert into tasks
(id, title) values
(:id, :title)
on conflict (id) do nothing
SQL;
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $task->id());
        $statement->bindValue(':title', $task->title());
        $statement->execute();
        $affectedRows = $statement->rowCount();
        if ($affectedRows === 0) {
            throw new TaskAlreadyExistsException();
        }
        if ($affectedRows !== 1) {
            throw new PdoReturnUnexpectedResultException(data_set: [$affectedRows]);
        }
    }

    public function createTaskId(): TaskId
    {
        $query = <<<'SQL'
select nextval('tasks_id_seq') as nextval
from (
    select pg_advisory_xact_lock(hashtextextended('tasks_id_seq', 0))
) as sequence_lock
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->execute();
        $data_set = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
        try {
            return TaskId::createFromMixedTypeValue($data_set[0]['nextval'] ?? null);
        } catch (TaskIdValidateException) {
            throw new PdoReturnUnexpectedResultException(data_set: $data_set);
        }
    }

    private function advanceSequencePast(int $taskId): void
    {
        $query = <<<'SQL'
select setval(
    'tasks_id_seq',
    greatest(:task_id, (select last_value from tasks_id_seq))
)
from (
    select pg_advisory_xact_lock(hashtextextended('tasks_id_seq', 0))
) as sequence_lock
SQL;
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':task_id', $taskId, PDO::PARAM_INT);
        $statement->execute();
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
        $affectedRows = $statement->rowCount();
        if ($affectedRows === 0) {
            throw new TaskNotFoundException();
        }
        if ($affectedRows !== 1) {
            throw new PdoReturnUnexpectedResultException(data_set: [$affectedRows]);
        }
    }
}
