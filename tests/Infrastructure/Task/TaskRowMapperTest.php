<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskTitleValidateException;
use App\Infrastructure\Task\TaskRowMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskRowMapperTest extends TestCase
{
    public function test_maps_a_pdo_row_to_a_task(): void
    {
        $task = (new TaskRowMapper())->map(['id' => '1', 'title' => 'stored task']);

        self::assertSame(1, $task->id());
        self::assertSame('stored task', $task->title());
    }

    #[DataProvider('invalidIds')]
    public function test_rejects_invalid_ids_with_the_precise_exception(mixed $id): void
    {
        $this->expectException(TaskIdValidateException::class);
        (new TaskRowMapper())->map(['id' => $id, 'title' => 'task']);
    }

    public static function invalidIds(): array
    {
        return [['1.5'], ['0'], [null]];
    }

    #[DataProvider('invalidTitles')]
    public function test_rejects_invalid_titles_with_the_precise_exception(mixed $title): void
    {
        $this->expectException(TaskTitleValidateException::class);
        (new TaskRowMapper())->map(['id' => '1', 'title' => $title]);
    }

    public static function invalidTitles(): array
    {
        return [['   '], [123], [null]];
    }
}
