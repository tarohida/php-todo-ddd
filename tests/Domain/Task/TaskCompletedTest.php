<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Exception\TaskCompletedValidateException;
use App\Domain\Task\TaskCompleted;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskCompletedTest extends TestCase
{
    public function test_it_accepts_strict_booleans(): void
    {
        self::assertFalse((new TaskCompleted(false))->completed());
        self::assertTrue((new TaskCompleted(true))->completed());
    }

    #[DataProvider('nonBooleanValues')]
    public function test_it_rejects_non_boolean_values(mixed $value): void
    {
        $this->expectException(TaskCompletedValidateException::class);
        TaskCompleted::createFromMixedTypeValue($value);
    }

    public static function nonBooleanValues(): array
    {
        return [[0], [1], ['false'], ['true'], [null], [[]]];
    }
}
