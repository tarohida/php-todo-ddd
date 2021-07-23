<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\Validate\TaskIdMustBePositiveNumberException;

/**
 * Class DeleteTaskService
 * @package App\Domain\Task
 */
class DeleteTaskService implements DeleteTaskServiceInterface
{
    /**
     * DeleteTaskService constructor.
     * @param TaskRepositoryInterface $repository
     */
    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }

    /**
     * @throws TaskIdMustBePositiveNumberException
     */
    public function delete(int $task_id): void
    {
        $task_id = new TaskId($task_id);
        $this->repository->delete($task_id);
    }
}