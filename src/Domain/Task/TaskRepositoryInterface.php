<?php


namespace App\Domain\Task;


interface TaskRepositoryInterface
{
    public function find(int $task_id): ?TaskInterface;

    public function list(): ?TaskListInterface;

    public function save(TaskInterface $task): void;
}