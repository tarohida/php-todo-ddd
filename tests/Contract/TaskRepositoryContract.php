<?php
declare(strict_types=1);

namespace Tests\Contract;

use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Exception\TaskAlreadyExistsException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;

abstract class TaskRepositoryContract extends TestCase
{
    private TaskRepositoryInterface $repository;

    final protected function setUp(): void
    {
        $this->repository = $this->createRepository();
        $this->resetRepository();
    }

    abstract protected function createRepository(): TaskRepositoryInterface;

    protected function resetRepository(): void
    {
    }

    final public function test_it_saves_and_lists_tasks_in_id_order(): void
    {
        $firstId = $this->repository->createTaskId();
        $secondId = $this->repository->createTaskId();
        $this->repository->save($this->task($secondId->id(), 'second'));
        $this->repository->save($this->task($firstId->id(), 'first'));

        self::assertSame([
            ['id' => $firstId->id(), 'title' => 'first'],
            ['id' => $secondId->id(), 'title' => 'second'],
        ], $this->taskData());
    }

    final public function test_it_allocates_monotonically_increasing_ids(): void
    {
        $first = $this->repository->createTaskId()->id();
        $second = $this->repository->createTaskId()->id();
        self::assertGreaterThan($first, $second);
    }

    final public function test_it_deletes_an_existing_task(): void
    {
        $id = $this->repository->createTaskId();
        $this->repository->save($this->task($id->id(), 'delete me'));

        $this->repository->delete($id);

        self::assertSame([], $this->taskData());
    }

    final public function test_deleting_a_missing_task_throws_not_found(): void
    {
        $this->expectException(TaskNotFoundException::class);

        $this->repository->delete($this->repository->createTaskId());
    }

    final public function test_saving_a_duplicate_id_throws_expected_exception(): void
    {
        $id = $this->repository->createTaskId();
        $this->repository->save($this->task($id->id(), 'original'));

        $this->expectException(TaskAlreadyExistsException::class);
        $this->repository->save($this->task($id->id(), 'duplicate'));
    }

    final public function test_allocation_advances_past_a_sparse_manually_saved_id(): void
    {
        $allocated = $this->repository->createTaskId();
        $arbitraryId = $allocated->id() + 100;
        $this->repository->save($this->task($arbitraryId, 'arbitrary'));

        self::assertGreaterThan($arbitraryId, $this->repository->createTaskId()->id());
    }

    /** @return list<array{id: int, title: string}> */
    private function taskData(): array
    {
        $data = [];
        foreach ($this->repository->list() as $task) {
            $data[] = ['id' => $task->id(), 'title' => $task->title()];
        }
        return $data;
    }

    private function task(int $id, string $title): Task
    {
        return new Task(new TaskId($id), new TaskTitle($title));
    }
}
