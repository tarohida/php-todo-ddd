<?php
declare(strict_types=1);


namespace App\Application\Action;


use App\Application\Action\Exception\TaskAlreadyExistsException\TaskAlreadyExistsException;
use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;

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
     * @throws TaskValidateException
     */
    public function create(TaskCreateCommandInterface $command): void
    {
        $id = $command->id();
        $title = $command->title();
        $task = new Task($id, $title);
        if ($this->task_service->taskExists($id)) {
            throw new TaskAlreadyExistsException($id);
        }
        $this->repository->save($task);
    }
}