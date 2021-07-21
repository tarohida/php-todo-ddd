<?php
declare(strict_types=1);


namespace App\Domain\Task\Exception\Validate;


use App\Exception\TodoAppException;

/**
 * Class TaskIdMustBePositiveNumberException
 * @package App\Domain\Task\Exception\Validate
 */
class TaskIdMustBePositiveNumberException extends TodoAppException
{
}