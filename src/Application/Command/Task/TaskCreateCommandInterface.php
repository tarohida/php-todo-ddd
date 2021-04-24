<?php
declare(strict_types=1);


namespace App\Application\Command\Task;


interface TaskCreateCommandInterface
{
    public function id(): int;
    public function title(): string;
}