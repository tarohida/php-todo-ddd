<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Exception;


use App\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class HtmlRendarFailedException
 * @package App\Application\Controller\Http\Exception
 */
class FailedToRenderHtmlException extends RuntimeException
{
    /**
     * @inheritDoc
     */
    public function getLoggingMessage(): string
    {
        return $this->getMessage();
    }

    /**
     * HtmlRendarFailedException constructor.
     * @param string $message
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct(string $message='', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}