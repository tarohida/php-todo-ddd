<?php
declare(strict_types=1);

namespace App\Application\Http\Middleware;

use Throwable;

final class RequestLogContext
{
    public const REQUEST_ATTRIBUTE = self::class;

    private ?string $exceptionClass = null;

    public function __construct(private readonly string $requestId)
    {
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function recordException(Throwable $exception): void
    {
        $this->exceptionClass = $exception::class;
    }

    public function exceptionClass(): ?string
    {
        return $this->exceptionClass;
    }
}
