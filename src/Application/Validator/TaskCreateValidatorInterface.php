<?php
declare(strict_types=1);


namespace App\Application\Validator;


use App\Application\Command\Task\TaskCreateCommandInterface;
use App\Application\Validator\Exception\ValidateException;

/**
 * Interface TaskCreateValidatorInterface
 * @package App\Application\Validator
 */
interface TaskCreateValidatorInterface extends ValidatorInterface
{
    /**
     * @param TaskCreateCommandInterface $command
     * @throws ValidateException
     */
    public function validate(TaskCreateCommandInterface $command): void;

}