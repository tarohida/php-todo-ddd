<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;

/**
 * Class TaskService
 * @package App\Domain\Task
 */
class TaskService implements TaskServiceInterface
{

    /**
     * TaskService constructor.
     * @param TaskRepositoryInterface $repository
     */
    public function __construct(
        private TaskRepositoryInterface $repository
    ){}

    public function taskExists(TaskId $id): bool
    {
        try {
            $this->repository->find($id);
            return true;
        } catch (SpecifiedTaskNotFoundException) {
            return false;
        }
    }
}