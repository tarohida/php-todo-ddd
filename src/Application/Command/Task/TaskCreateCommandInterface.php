<?php
declare(strict_types=1);


namespace App\Application\Command\Task;


/**
 * Interface TaskCreateCommandInterface
 * @package App\Application\Command\Task
 */
interface TaskCreateCommandInterface
{
    public function title(): string;
}