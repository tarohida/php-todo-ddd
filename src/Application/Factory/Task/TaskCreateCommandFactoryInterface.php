<?php
declare(strict_types=1);


namespace App\Application\Factory\Task;


use App\Application\Command\Task\TaskCreateCommandInterface;

interface TaskCreateCommandFactoryInterface
{
    public function factory(array $args, $post_data): TaskCreateCommandInterface;
}