<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\Exception\TaskValidateException;
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

    public function validParamsProvider(): array
    {
        return [
            [10000000, 'title1'],
            [1, '1'],
            [1, '☔']
        ];
    }

    public function notValidParamsProvider(): array
    {
        return [
            [0, 'title1'],
            [-1, 'title'],
            [1, '']
        ];
    }

    /**
     * @throws TaskValidateException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->id = 1;
        $this->title = 'title1';
        $this->task = new Task($this->id, $this->title);
    }

    /**
     * @dataProvider validParamsProvider
     * @param $id
     * @param $title
     * @throws TaskValidateException
     */
    public function test_construct($id, $title)
    {
        $this->assertInstanceOf(Task::class, new Task($id, $title));
    }

    /**
     * @dataProvider notValidParamsProvider
     * @param $id
     * @param $title
     */
    public function test_construct_throw_TaskValidateException($id, $title)
    {
        $this->expectException(TaskValidateException::class);
        new Task($id, $title);
    }

    public function invalid_construct_param_provider(): array
    {
        // id, title
        return [
            [null, 'title1'],
            [1, null],
            [null, null]
        ];
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
