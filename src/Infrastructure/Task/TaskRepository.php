<?php
declare(strict_types=1);


namespace App\Infrastructure\Task;


use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskIteratorInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\RepositoryBase;
use PDO;
use Tests\Infrastructure\Exception\RuntimeException\PdoReturnUnexpectedValueException;

/**
 * Class TaskRepository
 * @package App\Infrastructure\Task
 */
class TaskRepository extends RepositoryBase implements TaskRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    /**
     * @throws SpecifiedTaskNotFoundException
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
            throw new PdoReturnUnexpectedValueException($params, '1行以上の行が返されました');
        }
        if (!isset($params[0]['id']) || !isset($params[0]['title'])) {
            throw new PdoReturnUnexpectedValueException($params, '必要なパラメタが存在しません');
        }
        $id = $params[0]['id'];
        $title = $params[0]['title'];
        if (!is_numeric($id)) {
            throw new PdoReturnUnexpectedValueException($params, 'idの値が不正です');
        }
        return new Task((int)$id, $title);
    }

    public function list(): TaskIteratorInterface
    {
        // TODO: Implement list() method.
    }

    /**
     * @param TaskInterface $task
     */
    public function save(TaskInterface $task): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @param int $task_id
     */
    public function delete(int $task_id): void
    {
        // TODO: Implement delete() method.
    }
}