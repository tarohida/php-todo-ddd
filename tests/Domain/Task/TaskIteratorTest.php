<?php
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskIterator;
use App\Domain\Task\TaskIteratorInterface;
use PHPUnit\Framework\TestCase;
use TypeError;

class TaskIteratorTest extends TestCase
{
    private TaskIterator $task_iterator;

    public function setUp(): void
    {
        parent::setUp();
        $tasks = [
            $this->createMock(TaskInterface::class),
            $this->createMock(TaskInterface::class),
            $this->createMock(TaskInterface::class)
        ];
        $this->task_iterator = new TaskIterator($tasks);
    }

    public function test_implements_TaskIteratorInterface()
    {
        $this->assertInstanceOf(TaskIteratorInterface::class, $this->task_iterator);
    }

    public function test_construct_throws_TypeError_if_argument_array_has_a_class_that_is_not_a_TaskInterface()
    {
        $this->expectException(TypeError::class);
        $bad_arguments = [
            $this->createStub(TaskInterface::class),
            0
        ];
        new TaskIterator($bad_arguments);
    }

    public function test_foreach()
    {
        foreach ($this->task_iterator as $task) {
            $this->assertInstanceOf(TaskInterface::class, $task);
        }
    }
}
