<?php
declare(strict_types=1);

namespace App\Domain\Task\Exception\Validate;

use App\Domain\Task\Exception\TaskValidateException;

/**
 * Class TaskValidateByTitleException
 * @package App\Domain\Task\Exception
 */
class TaskTitleMustNotEmptyException extends TaskValidateException
{
}