<?php
declare(strict_types=1);

namespace App\Infrastructure\Pdo\Exception;

use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Throwable;

class PdoReturnUnexpectedResultException extends RuntimeException
{
    public function getDebugPrint(): string
    {
        if (is_null($this->data_set)) {
            return $this->getDebugNotImplementedMessage();
        }
        return $this->getDebugPrintedString($this->data_set);
    }

    #[Pure] public function __construct($message = '', $code = 0, Throwable $previous = null, private ?array $data_set = null)
    {
        parent::__construct($message, $code, $previous);
    }
}