<?php
declare(strict_types=1);

namespace Tests\Support;

use App\Domain\Task\TaskRepositoryInterface;
use Tests\Contract\TaskRepositoryContract;

final class InMemoryTaskRepositoryContractTest extends TaskRepositoryContract
{
    protected function createRepository(): TaskRepositoryInterface
    {
        return new InMemoryTaskRepository();
    }
}
