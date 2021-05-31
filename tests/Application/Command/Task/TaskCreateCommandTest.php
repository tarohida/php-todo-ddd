<?php
declare(strict_types=1);

namespace Tests\Application\Command\Task;

use App\Application\Command\Task\TaskCreateCommand;
use App\Application\Command\Task\TaskCreateCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskCreateCommandTest
 * @package Tests\Application\Command\Task
 */
class TaskCreateCommandTest extends TestCase
{
    private int $id;
    private string $title;
    private TaskCreateCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->id = 1;
        $this->title = 'title1';
        $this->command = new TaskCreateCommand($this->id, $this->title);
    }

    public function test_implements_TaskCreateCommandInterface()
    {
        $this->assertInstanceOf(TaskCreateCommandInterface::class, $this->command);
    }

    public function test_method_id()
    {
        $this->assertSame($this->id, $this->command->id());
    }

    public function test_method_title()
    {
        $this->assertSame($this->title, $this->command->title());
    }
}
