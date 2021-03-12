<?php
declare(strict_types=1);

namespace App\Domain\Task;

interface TaskServiceInterface
{
    public function __construct(TaskRepositoryInterface $repository);

    /**
     * @param TaskInterface $task
     * @return bool
     */
    public function exists(TaskInterface $task): bool;
}