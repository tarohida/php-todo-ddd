<?php


namespace Tests\Domain\Task;


use App\Domain\Task\Task;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    /**
     * @var TaskRepository|MockObject
     */
    private $repository;

    public function setUp(): void
    {

        $this->repository = $this->createStub(TaskRepository::class);
    }

    public function test_construct()
    {
        $this->assertInstanceOf(TaskService::class, new TaskService($this->repository));
    }

    /**
     * @dataProvider boolProvider
     * @param Task $input_task
     * @param Task|null $output_task
     * @param bool $expected
     */
    public function test_exists(Task $input_task, ?Task $output_task, bool $expected)
    {
        $this->repository->method('find')->willReturn($output_task);
        $task_service = new TaskService($this->repository);
        $this->assertSame($expected, $task_service->exists($input_task));
    }

    public function boolProvider(): array
    {
        return [
            [new Task(1, 'title'), new Task(1, 'title'), true],
            [new Task(1, 'title'), null, false]
        ];
    }
}