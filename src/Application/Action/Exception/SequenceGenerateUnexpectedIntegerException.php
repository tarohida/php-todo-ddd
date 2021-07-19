<?php
declare(strict_types=1);


namespace App\Application\Action\Exception;


use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class SequenseGenerateUnexpectedIntegerException
 * @package App\Application\Action\Exception
 */
class SequenceGenerateUnexpectedIntegerException extends RuntimeException
{
    /**
     * @inheritDoc
     */
    public function getLoggingMessage(): string
    {
        if (!is_null($this->integer)) {
            return 'debug param not set';
        }
        return print_r($this->integer, true);
    }

    /**
     * SequenceGenerateUnexpectedIntegerException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param int|null $integer
     */
    #[Pure] public function __construct(
        $message = '',
        $code = 0,
        Throwable $previous = null,
        private ?int $integer=null)
    {
        parent::__construct($message, $code, $previous);
    }
}