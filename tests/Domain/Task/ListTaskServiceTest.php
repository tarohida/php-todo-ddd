<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
declare(strict_types=1);

namespace Tests\Domain\Task;

use App\Domain\Task\ListTaskService;
use PHPUnit\Framework\TestCase;

class ListTaskServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_method_construct()
    {
        $service = new ListTaskService();
        $this->assertInstanceOf(ListTaskService::class, $service);
    }
}
