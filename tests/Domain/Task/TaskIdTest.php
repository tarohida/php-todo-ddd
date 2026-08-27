<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\TaskId;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TaskIdTest extends TestCase
{
    public function test_method_id()
    {
        $id = 1;
        $task_id = new TaskId($id);
        self::assertSame($id, $task_id->id());
    }

    #[DataProvider('invalidIntegerIds')]
    public function test_constructor_rejects_non_positive_ids(int $id): void
    {
        $this->expectException(TaskIdValidateException::class);
        new TaskId($id);
    }

    public static function invalidIntegerIds(): array
    {
        return [[0], [-1]];
    }

    public function test_factory_accepts_a_canonical_decimal_string(): void
    {
        $id = '1';
        $task_id = TaskId::createFromMixedTypeValue($id);
        self::assertSame(1, $task_id->id());
    }

    public function test_factory_accepts_php_int_max_exactly(): void
    {
        $taskId = TaskId::createFromMixedTypeValue((string) PHP_INT_MAX);

        self::assertSame(PHP_INT_MAX, $taskId->id());
    }

    public function test_factory_rejects_the_decimal_string_immediately_above_php_int_max(): void
    {
        $this->expectException(TaskIdValidateException::class);
        TaskId::createFromMixedTypeValue(self::decimalStringAbovePhpIntMax());
    }

    #[DataProvider('invalidMixedIds')]
    public function test_factory_rejects_values_that_are_not_positive_integers(mixed $id): void
    {
        $this->expectException(TaskIdValidateException::class);
        TaskId::createFromMixedTypeValue($id);
    }

    public static function invalidMixedIds(): array
    {
        return [
            [0],
            [-1],
            ['0'],
            ['-1'],
            ['1.5'],
            ['1e0'],
            [' 1'],
            ['1 '],
            ['01'],
            [true],
            [null],
        ];
    }

    private static function decimalStringAbovePhpIntMax(): string
    {
        $digits = str_split((string) PHP_INT_MAX);
        for ($index = count($digits) - 1; $index >= 0; --$index) {
            if ($digits[$index] !== '9') {
                $digits[$index] = (string) ((int) $digits[$index] + 1);
                return implode('', $digits);
            }
            $digits[$index] = '0';
        }

        return '1' . implode('', $digits);
    }
}
