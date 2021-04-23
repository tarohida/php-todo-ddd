<?php
declare(strict_types=1);


namespace App\Domain\Task;


interface TaskRepositoryInterface
{
    public function find(int $task_id): TaskInterface;
    public function list(): TaskIteratorInterface;
    public function save(TaskInterface $task);
    public function delete(int $task_id);
}