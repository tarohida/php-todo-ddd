<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\TaskValidateFailedWithIdException;

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
     * @throws TaskValidateFailedWithIdException
     */
    public function delete(int $task_id): void
    {
        TaskId::validate($task_id);
        $this->repository->delete($task_id);
    }
}