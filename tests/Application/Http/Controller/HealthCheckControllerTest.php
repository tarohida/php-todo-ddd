<?php
declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\HealthCheckController;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

final class HealthCheckControllerTest extends TestCase
{
    public function test_returns_ok_when_database_is_reachable(): void
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects(self::once())->method('execute')->willReturn(true);
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::once())->method('prepare')->with('SELECT 1')->willReturn($statement);

        $response = (new HealthCheckController(static fn (): PDO => $pdo))(
            (new ServerRequestFactory())->createServerRequest('GET', '/health'),
            new Response(),
            [],
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['status' => 'ok', 'database' => 'ok'],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
    }

    public function test_returns_service_unavailable_without_exposing_database_error(): void
    {
        $response = (new HealthCheckController(
            static fn (): PDO => throw new PDOException('password=secret host=private-db'),
        ))(
            (new ServerRequestFactory())->createServerRequest('GET', '/health'),
            new Response(),
            [],
        );

        self::assertSame(503, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['status' => 'unavailable', 'database' => 'unavailable'],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
        self::assertStringNotContainsString('secret', (string) $response->getBody());
        self::assertStringNotContainsString('private-db', (string) $response->getBody());
    }
}
