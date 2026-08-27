<?php
declare(strict_types=1);

namespace App\Domain\Task\Exception;

use App\Exception\ApplicationException;

final class TaskNotFoundException extends ApplicationException
{
}
