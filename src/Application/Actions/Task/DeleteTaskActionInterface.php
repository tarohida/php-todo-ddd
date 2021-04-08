<?php
declare(strict_types=1);


namespace App\Application\Actions\Task;


interface DeleteTaskActionInterface
{
    public function action($task_id): void;
}