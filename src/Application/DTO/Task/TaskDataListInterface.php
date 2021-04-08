<?php
declare(strict_types=1);

namespace App\Application\DTO\Task;

use Iterator;

interface TaskDataListInterface extends Iterator
{
    public function isEmpty(): bool;
}