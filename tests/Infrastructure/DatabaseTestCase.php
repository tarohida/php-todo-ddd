<?php
declare(strict_types=1);

namespace Tests\Infrastructure;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Class DatabaseTestCase
 * @package Tests\Infrastructure
 */
class DatabaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getPdo(): PDO
    {
        $db_host = $_ENV['db_host'];
        $db_name = $_ENV['db_name'];
        $db_user = $_ENV['db_user'];
        $db_password = $_ENV['db_password'];
        return new PDO(
            "pgsql:host=$db_host;dbname=$db_name;",
            $db_user,
            $db_password
        );
    }

}
