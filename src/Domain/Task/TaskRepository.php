<?php


namespace App\Domain\Task;


interface TaskRepository
{
    public function find(int $task_id): ?Task;

}