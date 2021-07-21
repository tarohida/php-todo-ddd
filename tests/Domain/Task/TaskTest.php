<?php
/** @noinspection  PhpUnhandledExceptionInspection*/
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Exception\Validate\TaskIdMustBePositiveNumberException;
use App\Domain\Task\Exception\Validate\TaskTitleMustNotEmptyException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskTest
 * @package Tests\Domain\Task
 */
class TaskTest extends TestCase
{
    private Task $task;
    private int $id;
    private string $title;

    public function notValidIdProvider(): array
    {
        return [
            [0, 'title1'],
            [-1, 'title'],
        ];
    }

    public function notValidTitleProvider(): array
    {
        return [
            [1, '']
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->id = 1;
        $this->title = 'title1';
        $this->task = new Task($this->id, $this->title);
    }

    /**
     * @dataProvider notValidIdProvider
     * @param $id
     * @param $title
     */
    public function test_validate_id($id, $title)
    {
        $this->expectException(TaskIdMustBePositiveNumberException::class);
        new Task($id, $title);
    }

    /**
     * @dataProvider notValidTitleProvider
     * @param $id
     * @param $title
     */
    public function test_validate_title($id, $title)
    {
        $this->expectException(TaskTitleMustNotEmptyException::class);
        new Task($id, $title);
    }

    public function test_implements_TaskInterface()
    {
        $this->assertInstanceOf(TaskInterface::class, $this->task);
    }

    public function test_method_id()
    {
        $this->assertSame($this->id, $this->task->id());
    }

    public function test_method_title()
    {
        $this->assertSame($this->title, $this->task->title());
    }
}
