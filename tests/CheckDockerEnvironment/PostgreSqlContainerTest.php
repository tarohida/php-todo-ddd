<?php
declare(strict_types=1);

namespace Tests\CheckDockerEnvironment;

use PDO;
use PHPUnit\Framework\TestCase;

class PostgreSqlContainerTest extends TestCase
{
    public function test_connect_to_postgresql_with_pdo(): void
    {
        $dbHost = $this->requiredEnvironmentVariable('DB_HOST');
        $dbName = $this->requiredEnvironmentVariable('DB_NAME');
        $dbUser = $this->requiredEnvironmentVariable('DB_USER');
        $dbPassword = $this->requiredEnvironmentVariable('DB_PASSWORD');

        $pdo = new PDO(
            "pgsql:host={$dbHost};dbname={$dbName};",
            $dbUser,
            $dbPassword
        );

        $this->assertInstanceOf(PDO::class, $pdo);
    }

    private function requiredEnvironmentVariable(string $name): string
    {
        $value = getenv($name);

        $this->assertIsString($value, "{$name} must be set in the test environment.");
        $this->assertNotSame('', $value, "{$name} must not be empty in the test environment.");

        return $value;
    }
}
