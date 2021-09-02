<?php
declare(strict_types=1);

namespace App\Domain\Task;

class ListTaskService
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }
}