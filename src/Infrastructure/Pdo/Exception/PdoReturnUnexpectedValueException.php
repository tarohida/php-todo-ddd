<?php
declare(strict_types=1);


namespace App\Infrastructure\Pdo\Exception;


use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class PdoReturnUnexpectedValueException
 * @package App\Infrastructure\Pdo\Exception
 */
class PdoReturnUnexpectedValueException extends RuntimeException
{
    /**
     * @inheritDoc
     */
    public function getLoggingMessage(): string
    {
        return print_r($this->data_set, true) . print_r($this->prepared_params, true);
    }

    #[Pure] public function __construct(
        private array $data_set,
        private array $prepared_params,
        Throwable $previous = null
    ) {
        parent::__construct(previous: $previous);
    }
}