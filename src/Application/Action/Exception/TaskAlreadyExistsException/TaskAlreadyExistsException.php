<?php
declare(strict_types=1);


namespace App\Application\Action\Exception\TaskAlreadyExistsException;


use App\Exception\TodoAppException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class TaskAlreadyExistsException
 * @package App\Application\Action\Exception\TaskAlreadyExistsException
 */
class TaskAlreadyExistsException extends TodoAppException
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    #[Pure] public function __construct(
        private int $task_id,
        $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getTaskId(): int
    {
        return $this->task_id;
    }
}