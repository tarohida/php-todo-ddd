<?php


namespace Tests\Domain\Task;


use App\Domain\Task\Task;
use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use App\Domain\Task\TaskServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    /**
     * @var TaskRepositoryInterface|MockObject
     */
    private $repository;

    public function setUp(): void
    {

        $this->repository = $this->createStub(TaskRepositoryInterface::class);
    }

    public function test_construct()
    {
        $this->assertInstanceOf(TaskService::class, new TaskService($this->repository));
    }

    public function test_implements_TaskServiceInterface()
    {
        $this->assertInstanceOf(TaskServiceInterface::class, new TaskService($this->repository));
    }
    /**
     * @dataProvider boolProvider
     * @param TaskInterface $input_task
     * @param TaskInterface|null $output_task
     * @param bool $expected
     */
    public function test_exists(TaskInterface $input_task, ?TaskInterface $output_task, bool $expected)
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