<?php
declare(strict_types=1);


namespace App\Domain\Task;


/**
 * Interface TaskInterface
 * @package App\Domain\Task
 */
interface TaskInterface
{
    public function id(): int;
    public function title(): string;
}