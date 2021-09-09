<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

use App\Domain\Task\TaskList;
use App\Infrastructure\Pdo\Exception\PdoReturnUnexpectedResultException;
use PDO;

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
             } catch (Exception\TaskValidateException $e) {
                 throw new PdoReturnUnexpectedResultException(previous: $e, data_set:$data_set);
             }
        }
        return new TaskList($tasks);
    }
}