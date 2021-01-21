<?php


namespace Tests\DevelopmentEnvCheck;


use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Class PostgreSqlTest
 * @package Tests\DevelopmentEnvCheck
 *
 * if you failed to this tests, you may have postgresql connection problems.
 */
class PostgreSqlTest extends TestCase
{
    public function test_check_connect_to_postgresql()
    {
        $pdo = new PDO(
            'pgsql:host=db;dbname=dbname;',
            'dbuser',
            'dbpassword',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        self::assertInstanceOf(PDO::class, $pdo);
    }

}