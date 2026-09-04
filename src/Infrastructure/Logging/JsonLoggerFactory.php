<?php
declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class JsonLoggerFactory
{
    /**
     * @param resource|string $stream
     */
    public function create(mixed $stream = 'php://stderr'): LoggerInterface
    {
        $handler = new StreamHandler($stream, Level::Info);
        $handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES, true));

        return new Logger('api', [$handler]);
    }
}
