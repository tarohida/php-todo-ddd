<?php
declare(strict_types=1);

namespace Tests\Http\Health;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

final class HealthApiContractTest extends TestCase
{
    public function test_health_endpoint_reports_application_and_database_ready(): void
    {
        $baseUrl = rtrim($_ENV['HTTP_TEST_BASE_URL'] ?? 'http://web', '/');
        $response = (new Client(['http_errors' => false]))->get($baseUrl . '/health', [
            'headers' => ['X-Request-Id' => 'health-contract-request'],
        ]);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('health-contract-request', $response->getHeaderLine('X-Request-Id'));
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['status' => 'ok', 'database' => 'ok'],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
    }
}
