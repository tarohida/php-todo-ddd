<?php
declare(strict_types=1);

namespace App\Domain\Task;

class CreateTaskService
{
    public function serve(TaskTitle $title)
    {
        // todo: 呼び出し方を変更
        $task = new Task(
            $this->repository->createTaskId()->id(),
            $title->title()
        );
        $this->repository->save($task);
    }

    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }
}