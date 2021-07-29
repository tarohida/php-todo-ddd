<?php
declare(strict_types=1);


namespace App\Domain\Task\Exception;


use App\Exception\TodoAppException;
use Throwable;

class SpecifiedTaskNotFoundException extends TodoAppException implements SpecifiedTaskNotFoundExceptionInterface
{
    private int $id;

    public function getSpecifiedId(): int
    {
        return $this->id;
    }

    public function __construct(int $specified_id, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->id = $specified_id;
        parent::__construct($message, $code, $previous);
    }
}