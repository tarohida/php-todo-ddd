<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Logging;

use App\Infrastructure\Logging\JsonLoggerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class JsonLoggerFactoryTest extends TestCase
{
    public function test_creates_a_psr_logger_that_writes_one_json_object_per_record(): void
    {
        $stream = fopen('php://memory', 'w+');
        self::assertIsResource($stream);
        $logger = (new JsonLoggerFactory())->create($stream);

        self::assertInstanceOf(LoggerInterface::class, $logger);
        $logger->info('HTTP request completed', ['request_id' => 'request-123']);
        rewind($stream);
        $line = stream_get_contents($stream);
        self::assertIsString($line);
        self::assertStringEndsWith("\n", $line);
        $record = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('HTTP request completed', $record['message']);
        self::assertSame('request-123', $record['context']['request_id']);
    }
}
