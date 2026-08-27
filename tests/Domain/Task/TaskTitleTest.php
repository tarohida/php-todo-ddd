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
}
