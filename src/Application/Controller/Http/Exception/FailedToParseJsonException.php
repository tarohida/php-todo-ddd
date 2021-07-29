<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Exception;


use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class FailedToParseJsonException
 * @package App\Application\Controller\Http\Exception
 */
class FailedToParseJsonException extends RuntimeException
{
    /**
     * @inheritDoc
     */
    #[Pure] public function getLoggingMessage(): string
    {
        return print_r($this->json_last_error_code, true)
            . print_r($this->converted_params, true);
    }

    /**
     * FailedToParseJsonException constructor.
     * @param int $json_last_error_code
     * @param mixed $converted_params
     * @param int $code
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct(
        private int $json_last_error_code,
        private mixed $converted_params,
        $code = 0, Throwable $previous = null)
    {
        parent::__construct('', $code, $previous);
    }
}