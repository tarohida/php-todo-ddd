<?php
declare(strict_types=1);

namespace App\Application\Task;

use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;

class ListTasks
{
    public function __construct(private TaskRepositoryInterface $repository) {}

    public function execute(): TaskList
    {
        return $this->repository->list();
    }
}
