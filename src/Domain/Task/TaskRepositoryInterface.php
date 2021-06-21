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
     * @param int $task_id
     * @return TaskInterface
     * @throws SpecifiedTaskNotFoundException
     */
    public function find(int $task_id): TaskInterface;
    public function list(): TaskIteratorInterface;

    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function save(TaskInterface $task): void;

    /**
     * @param int $task_id
     * @return mixed
     */
    public function delete(int $task_id): void;
}