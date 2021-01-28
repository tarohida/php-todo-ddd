<?php


namespace App\Domain\Task;


interface TaskRepository
{
    public function find(string $task_id): ?Task;

}