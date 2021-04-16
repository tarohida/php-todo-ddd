<?php
declare(strict_types=1);


namespace App\Application\Actions\Task;


use App\Domain\Task\TaskRepositoryInterface;

class DeleteTaskAction implements DeleteTaskActionInterface
{
    /**
     * @var TaskRepositoryInterface
     */
    private TaskRepositoryInterface $repository;

    /**
     * DeleteTaskAction constructor.
     * @param TaskRepositoryInterface $repository
     */
    public function __construct(TaskRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function action($task_id): void
    {
        $this->repository->delete($task_id);
    }
}