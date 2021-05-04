<?php
declare(strict_types=1);


namespace Tests\Infrastructure\Exception\RuntimeException;


use App\Exception\RuntimeException;
use Throwable;

class PdoReturnUnexpectedValueException extends RuntimeException
{
    private mixed $params;

    public function getParams(): mixed
    {
        return $this->params;
    }

    public function getLoggingMessage(): string
    {
        return print_r($this->params, true);
    }

    public function __construct(mixed $params, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->params = $params;
        parent::__construct($message, $code, $previous);
    }

}