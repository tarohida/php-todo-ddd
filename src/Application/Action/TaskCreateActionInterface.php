<?php
declare(strict_types=1);


namespace App\Application\Action;


use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\Exception\Validate\TaskTitleMustNotEmptyException;
use App\Domain\Task\TaskInterface;

/**
 * Interface TaskCreateActionInterface
 * @package App\Application\Action
 */
interface TaskCreateActionInterface
{
    /**
     * @param TaskCreateCommandInterface $command
     * @return TaskInterface
     * @throws TaskTitleMustNotEmptyException
     */
    public function create(TaskCreateCommandInterface $command): TaskInterface;
}