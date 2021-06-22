<?php
declare(strict_types=1);


namespace App\Infrastructure\Task;


use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskIterator;
use App\Domain\Task\TaskIteratorInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Exception\LogicException;
use App\Infrastructure\Pdo\Exception\NotAffectedException;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedValueException;
use App\Infrastructure\Pdo\Exception\TooAffectedException;
use PDO;

/**
 * Class TaskRepository
 * @package App\Infrastructure\Task
 */
class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        protected PDO $pdo
    ) {}

    /**
     * @throws SpecifiedTaskNotFoundException|TaskValidateException
     */
    public function find(int $task_id): TaskInterface
    {
        $query = <<<SQL
select id, title from tasks
where id = :id
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->bindValue(':id', $task_id);
        $pdo_statement->execute();
        $params = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($params) === 0) {
            throw new SpecifiedTaskNotFoundException($task_id);
        }
        if (count($params) != 1) {
            throw new PdoReturnUnexpectedValueException($params, [$task_id]);
        }
        if (!isset($params[0]['id']) || !isset($params[0]['title'])) {
            throw new PdoReturnUnexpectedValueException($params, [$task_id]);
        }
        $id = $params[0]['id'];
        $title = $params[0]['title'];
        if (!is_numeric($id)) {
            throw new PdoReturnUnexpectedValueException($params, [$task_id]);
        }
        return new Task((int)$id, $title);
    }

    public function list(): TaskIteratorInterface
    {
        $query = <<<'SQL'
select id ,title from tasks
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->execute();
        $data_set = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
        $tasks = [];
        foreach ($data_set as $data) {
            if (!isset($data['id']) || !isset($data['title'])) {
                throw new PdoReturnUnexpectedValueException($data_set, []);
            }
            if (!is_numeric($data['id'])) {
                throw new PdoReturnUnexpectedValueException($data_set, []);
            }
            try {
                $tasks[] = new Task((int)$data['id'], $data['title']);
            } catch (TaskValidateException $e) {
                throw new PdoReturnUnexpectedValueException($data_set, [], previous: $e);
            }
        }
        return new TaskIterator($tasks);

    }

    /**
     * @param TaskInterface $task
     */
    public function save(TaskInterface $task): void
    {
        $sql = <<<'SQL'
insert into tasks
(id, title) values 
(:id, :title)
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $task->id());
        $statement->bindValue(':title', $task->title());
        $statement->execute();
        $affected_row_count = $statement->rowCount();
        if ($affected_row_count === 0) {
            throw new NotAffectedException([$task->id(), $task->title()]);
        }
        if ($affected_row_count !== 1) {
            throw new TooAffectedException([$task->id(), $task->title()], $affected_row_count);
        }
    }

    /**
     * @param int $task_id
     */
    public function delete(int $task_id): void
    {
        throw new LogicException();
    }

    public function getNextValueInSequence(): int
    {
        $query = <<<'SQL'
select nextval('tasks_id_seq')
SQL;
        $pdo_statement = $this->pdo->prepare($query);
        $pdo_statement->execute();
        $data_set = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($data_set) !== 1 ||
            !isset($data_set[0]['nextval']) ||
            !is_numeric($data_set[0]['nextval']))
        {
            throw new PdoReturnUnexpectedValueException($data_set, []);
        }
        return (int)$data_set[0]['nextval'];
    }
}