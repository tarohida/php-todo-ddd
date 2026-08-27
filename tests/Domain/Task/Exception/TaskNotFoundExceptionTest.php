<?php
declare(strict_types=1);

namespace Tests\Domain\Task\Exception;

use App\Domain\Task\Exception\TaskNotFoundException;
use App\Exception\ApplicationExceptionInterface;
use PHPUnit\Framework\TestCase;

final class TaskNotFoundExceptionTest extends TestCase
{
    public function test_implements_the_application_exception_contract(): void
    {
        self::assertInstanceOf(
            ApplicationExceptionInterface::class,
            new TaskNotFoundException(),
        );
    }
}
