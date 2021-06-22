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
    private string $title;
    private TaskCreateCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = 'title1';
        $this->command = new TaskCreateCommand($this->title);
    }

    public function test_implements_TaskCreateCommandInterface()
    {
        $this->assertInstanceOf(TaskCreateCommandInterface::class, $this->command);
    }

    public function test_method_title()
    {
        $this->assertSame($this->title, $this->command->title());
    }
}
