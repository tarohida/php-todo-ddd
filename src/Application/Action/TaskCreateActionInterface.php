<?php
declare(strict_types=1);


namespace App\Application\Action;


use App\Application\Action\Exception\TaskAlreadyExistsException\TaskAlreadyExistsException;
use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Domain\Task\Exception\TaskValidateException;

/**
 * Interface TaskCreateActionInterface
 * @package App\Application\Action
 */
interface TaskCreateActionInterface
{
    /**
     * @param TaskCreateCommandInterface $command
     * @return void
     * @throws  TaskAlreadyExistsException
     * @throws  TaskValidateException
     */
    public function create(TaskCreateCommandInterface $command): void;
}