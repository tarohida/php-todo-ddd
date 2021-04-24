<?php
declare(strict_types=1);


namespace App\Application\Action;


use App\Application\Command\Task\TaskCreateCommandInterface;

interface TaskCreateActionInterface
{
    public function create(TaskCreateCommandInterface $command);
}