<?php
declare(strict_types=1);

namespace App\Application\Http\Controller\Exception;

use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Throwable;

class JsonConvertFailedException extends RuntimeException
{
    public function getDebugPrint(): string
    {
        if (is_null($this->params)) {
            return $this->getDebugNotImplementedMessage();
        }
        return $this->getDebugPrintedString($this->params);
    }

    #[Pure] public function __construct($message = '', $code = 0, Throwable $previous = null, private ?array $params=null)
    {
        parent::__construct($message, $code, $previous);
    }
}