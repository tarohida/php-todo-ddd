<?php
declare(strict_types=1);

namespace Tests\Application\Controller\Http\Api;

use App\Application\Controller\Http\Api\TaskDeleteApiController;
use App\Application\Controller\Http\Api\TaskDeleteApiControllerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskDeleteApiControllerTest
 * @package Tests\Application\Controller\Http\Api
 */
class TaskDeleteApiControllerTest extends TestCase
{
    private TaskDeleteApiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TaskDeleteApiController();
    }

    public function test_implement_interface()
    {
        $this->assertInstanceOf(TaskDeleteApiControllerInterface::class, $this->controller);
    }
}
