<?php
declare(strict_types=1);

namespace App\Domain\Task\Exception\Validate;

use App\Exception\TodoAppException;

/**
 * Class TaskValidateByTitleException
 * @package App\Domain\Task\Exception
 */
class TaskTitleMustNotEmptyException extends TodoAppException
{
}