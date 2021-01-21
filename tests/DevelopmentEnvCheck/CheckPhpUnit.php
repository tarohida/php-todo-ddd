<?php


namespace Tests\DevelopmentEnvCheck;


use PHPUnit\Framework\TestCase;

/**
 * Class CheckPHP
 * @package Tests\DevelopmentEnvCheck
 *
 * if you get error in this test, your php environment or phpunit environment may something wrong.
 */
class CheckPhpUnit extends TestCase
{
    public function test_check_phpunit()
    {
        $this->assertTrue(true);
    }
}