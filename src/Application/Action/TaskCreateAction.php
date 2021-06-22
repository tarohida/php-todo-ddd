<?php
declare(strict_types=1);


namespace App\Application\Action;


use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\Exception\TaskValidateException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskRepositoryInterface;

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
    ) {}

    /**
     * @param TaskCreateCommandInterface $command
     * @return TaskInterface
     * @throws TaskValidateException
     */
    public function create(TaskCreateCommandInterface $command): TaskInterface
    {
        $title = $command->title();
        $id = $this->repository->getNextValueInSequence();
        $task = new Task($id, $title);
        $this->repository->save($task);
        return $task;
    }
}