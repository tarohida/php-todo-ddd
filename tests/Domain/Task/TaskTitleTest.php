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

class TaskTitleTest extends TestCase
{
    public function test_method_title()
    {
        $string = 'title1';
        $title = new TaskTitle($string);
        $this->assertSame($string, $title->title());
    }

    public function test_construct_throw_Exception()
    {
        $this->expectException(TaskTitleValidateException::class);
        $string = '';
        new TaskTitle($string);
    }
}
