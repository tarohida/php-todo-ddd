<?php
declare(strict_types=1);


namespace App\Infrastructure\Pdo\Exception;


use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;

/**
 * Class NotAffectedException
 * @package App\Infrastructure\Pdo\Exception
 */
class NotAffectedException extends RuntimeException
{
    public function getLoggingMessage(): string
    {
        return print_r($this->prepared_params, true);
    }

    #[Pure] public function __construct(
        private array $prepared_params)
    {
        parent::__construct();
    }
}