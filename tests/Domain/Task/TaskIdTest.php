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

class TaskIdTest extends TestCase
{
    public function test_method_id()
    {
        $id = 1;
        $task_id = new TaskId($id);
        self::assertSame($id, $task_id->id());
    }

    public function test_method_id_throw_Exception()
    {
        $this->expectException(TaskIdValidateException::class);
        $id = -1;
        new TaskId($id);
    }

    public function test_method_createFromPdoResultRows()
    {
        $id = '1';
        $task_id = TaskId::createFromMixedTypeValue($id);
        self::assertSame(1, $task_id->id());
    }

}
