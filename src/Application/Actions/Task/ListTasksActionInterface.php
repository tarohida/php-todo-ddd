<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\DTO\Task\TaskDataListInterface;

interface ListTasksActionInterface
{
    public function action(): TaskDataListInterface;
}