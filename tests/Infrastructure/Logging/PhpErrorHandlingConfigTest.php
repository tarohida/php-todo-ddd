<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Logging;

use PHPUnit\Framework\TestCase;

final class PhpErrorHandlingConfigTest extends TestCase
{
    public function test_runtime_errors_are_logged_but_never_rendered_into_api_responses(): void
    {
        $config = parse_ini_file(dirname(__DIR__, 3) . '/.docker/conf/php/php.ini');
        self::assertIsArray($config);

        self::assertSame('0', (string) ($config['display_errors'] ?? ''));
        self::assertSame('0', (string) ($config['display_startup_errors'] ?? ''));
        self::assertSame('1', (string) ($config['log_errors'] ?? ''));
    }
}
