<?php
declare(strict_types=1);

namespace Tests\Application\Controller\Http\Api;

use App\Application\Controller\Http\Api\DeleteTaskApiController;
use App\Application\Controller\Http\Api\DeleteTaskApiControllerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskDeleteApiControllerTest
 * @package Tests\Application\Controller\Http\Api
 */
class TaskDeleteApiControllerTest extends TestCase
{
    private DeleteTaskApiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new DeleteTaskApiController();
    }

    public function test_implement_interface()
    {
        $this->assertInstanceOf(DeleteTaskApiControllerInterface::class, $this->controller);
    }
}
