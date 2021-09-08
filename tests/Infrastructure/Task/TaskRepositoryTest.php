<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Infrastructure\Task;

use App\Infrastructure\Task\TaskRepository;

use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    public function test_method_construct()
    {
        $pdo = $this->createMock(\PDO::class);
        $repository = new TaskRepository($pdo);
        $this->assertInstanceOf(TaskRepository::class, $repository);
    }
}
