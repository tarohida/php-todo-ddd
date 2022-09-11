<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tarohida\Tests\PhpTodoDdd\Infrastructure;

use PHPUnit\Framework\TestCase;
use Tarohida\PhpTodoDdd\Domain\Exception\TaskNotFoundException;
use Tarohida\PhpTodoDdd\Domain\Task;
use Tarohida\PhpTodoDdd\Infrastructure\TaskInMemoryDB;

class TaskRepositoryTest extends TestCase
{
    public function test_保存したタスクが取得できる()
    {
        $id = 1;
        $repository = $this->getTaskRepository();
        $task = $this->saveTaskWith($id, $repository);
        $returned_task = $repository->find($id);
        $this->assertSame($task, $returned_task);
    }

    /**
     * @return TaskInMemoryDB
     */
    private function getTaskRepository(): TaskInMemoryDB
    {
        return new TaskInMemoryDB();
    }

    /**
     * @param int $id
     * @param TaskInMemoryDB $repository
     * @return Task
     */
    private function saveTaskWith(int $id, TaskInMemoryDB $repository): Task
    {
        $task = new Task($id, 'test');
        $repository->save($task);
        return $task;
    }

    public function test_保存したタスクがリストに含まれる()
    {
        $task1 = new Task(1, 'test1');
        $task2 = new Task(2, 'test2');
        $task3 = new Task(3, 'test3');
        $repository = $this->getTaskRepository();
        $expected = [$task1, $task2, $task3];
        $this->saveTasks($repository, $task1, $task2, $task3);
        $list = $repository->list();
        $this->assertSame($expected, $list);
    }

    /**
     * @param TaskInMemoryDB $repository
     * @param Task $task1
     * @param Task $task2
     * @param Task $task3
     * @return void
     */
    private function saveTasks(TaskInMemoryDB $repository, Task $task1, Task $task2, Task $task3): void
    {
        $repository->save($task1);
        $repository->save($task2);
        $repository->save($task3);
    }

    public function test_削除されたタスクを取得しようとすると例外が返る()
    {
        $this->expectException(TaskNotFoundException::class);
        $id = 1;
        $repository = $this->getTaskRepository();
        $this->saveTaskWith($id, $repository);
        $repository->delete($id);
        $repository->find($id);
    }

    public function test_削除されたタスクがリストに含まれない()
    {
        $task1 = new Task(1, 'test1');
        $task2 = new Task(2, 'test2');
        $task3 = new Task(3, 'test3');
        $repository = $this->getTaskRepository();
        $expected = [$task1, 2 => $task3];
        $this->saveTasks($repository, $task1, $task2, $task3);
        $repository->delete(2);
        $list = $repository->list();
        $this->assertSame($expected, $list);
    }
}
