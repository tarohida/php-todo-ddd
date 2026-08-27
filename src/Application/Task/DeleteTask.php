<?php
declare(strict_types=1);

namespace App\Application\Task;

use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;

class DeleteTask
{
    public function __construct(private TaskRepositoryInterface $repository) {}

    public function execute(TaskId $id): void
    {
        $this->repository->delete($id);
    }
}
