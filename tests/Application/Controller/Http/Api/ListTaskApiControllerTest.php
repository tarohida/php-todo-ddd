<?php
declare(strict_types=1);

namespace Tests\Application\Controller\Http\Api;

use App\Application\Controller\Http\Api\ListTaskApiController;
use App\Application\Controller\Http\Api\ListTaskApiControllerInterface;
use App\Domain\Task\TaskRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ListTaskApiControllerTest
 * @package Tests\Application\Controller\Http\Api
 */
class ListTaskApiControllerTest extends TestCase
{
    private ListTaskApiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $this->controller = new ListTaskApiController($repository);
    }

    public function test_implement_interface()
    {
        $this->assertInstanceOf(ListTaskApiControllerInterface::class, $this->controller);
    }
}
