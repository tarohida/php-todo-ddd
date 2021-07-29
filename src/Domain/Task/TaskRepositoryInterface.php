<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\SpecifiedTaskNotFoundException;

/**
 * Interface TaskRepositoryInterface
 * @package App\Domain\Task
 */
interface TaskRepositoryInterface
{
    /**
     * @param TaskId $task_id
     * @return TaskInterface
     * @throws SpecifiedTaskNotFoundException
     */
    public function find(TaskId $task_id): TaskInterface;
    public function list(): TaskIteratorInterface;

    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function save(TaskInterface $task): void;

    /**
     * @param TaskId $task_id
     */
    public function delete(TaskId $task_id): void;
    public function getNextValueInSequence(): int;
}