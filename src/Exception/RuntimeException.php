<?php
declare(strict_types=1);

namespace App\Exception;

abstract class RuntimeException extends \RuntimeException implements ApplicationExceptionInterface
{
    abstract public function getDebugPrint() :string;
    protected function getDebugPrintedString(mixed $param): string
    {
        return print_r($param, true);
    }
    protected function getDebugNotImplementedMessage(): string
    {
        return 'debug not implemented';
    }
}