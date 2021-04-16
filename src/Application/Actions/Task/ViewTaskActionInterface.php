<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\DTO\Task\TaskData;

interface ViewTaskActionInterface
{
    public function action(int $id): TaskData;
}