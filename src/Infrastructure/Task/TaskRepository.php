<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

use App\Domain\Task\TaskList;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedResultException;
use PDO;
use RuntimeException;

class TaskRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function list(): TaskList
    {
        $sql = <<< 'SQL'
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
             } catch (RuntimeException) {
                 throw new PdoReturnUnexpectedResultException(data_set:$data_set);
             }
        }
        return new TaskList($tasks);
    }
}