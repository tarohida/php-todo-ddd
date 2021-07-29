<?php
declare(strict_types=1);


namespace App\Infrastructure\Pdo\Exception;


use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;

/**
 * Class TooAffectedException
 * @package App\Infrastructure\Pdo\Exception
 */
class TooAffectedException extends RuntimeException
{
    public function getLoggingMessage(): string
    {
        return print_r($this->prepared_params, true) .
            print_r($this->affected_row_count, true);
    }

    #[Pure] public function __construct(
        private array $prepared_params,
        private int $affected_row_count
    )
    {
        parent::__construct();
    }
}