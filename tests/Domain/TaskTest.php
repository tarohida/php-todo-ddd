<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tarohida\Tests\PhpTodoDdd\Domain;

use PHPUnit\Framework\TestCase;
use Tarohida\PhpTodoDdd\Domain\Task;

class TaskTest extends TestCase
{
    public function test_construct()
    {
        $this->assertInstanceOf(Task::class, new Task(1, 'title'));
    }
}
