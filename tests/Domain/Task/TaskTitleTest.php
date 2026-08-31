<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Exception\TaskTitleValidateException;
use App\Domain\Task\TaskTitle;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TaskTitleTest extends TestCase
{
    public function test_method_title()
    {
        $string = 'title1';
        $title = new TaskTitle($string);
        $this->assertSame($string, $title->title());
    }

    #[DataProvider('blankTitles')]
    public function test_constructor_rejects_blank_titles(string $title): void
    {
        $this->expectException(TaskTitleValidateException::class);
        new TaskTitle($title);
    }

    public static function blankTitles(): array
    {
        return [[''], ['   '], ["\t\n"]];
    }

    public function test_constructor_rejects_titles_longer_than_database_limit(): void
    {
        $this->expectException(TaskTitleValidateException::class);
        new TaskTitle(str_repeat('あ', 256));
    }

    public function test_constructor_accepts_a_multibyte_title_at_database_limit(): void
    {
        self::assertSame(str_repeat('あ', 255), (new TaskTitle(str_repeat('あ', 255)))->title());
    }
}
