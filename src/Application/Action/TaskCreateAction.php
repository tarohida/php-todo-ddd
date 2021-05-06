<?php
declare(strict_types=1);


namespace App\Application\Action;


use App\Application\Action\Exception\TaskAlreadyExistsException\TaskAlreadyExistsException;
use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;
use PDOException;

/**
 * Class TaskCreateAction
 * @package App\Application\Action
 */
class TaskCreateAction implements TaskCreateActionInterface
{
    /**
     * TaskCreateAction constructor.
     */
    public function __construct(
        private TaskRepositoryInterface $repository,
        private TaskServiceInterface $task_service
    ) {}

    /**
     * @param TaskCreateCommandInterface $command
     * @throws TaskAlreadyExistsException
     */
    public function create(TaskCreateCommandInterface $command)
    {
        try {
            $id = $command->id();
            $title = $command->title();
            $this->repository->beginTransaction();
            if ($this->task_service->taskExists($id)) {
                throw new TaskAlreadyExistsException($id);
            }
            $task = new Task($id, $title);
            $this->repository->save($task);
            $this->repository->commit();
        } catch (PDOException $ex) {
            $this->repository->rollback();
            throw $ex;
        }
    }
}