<?php
/** @noinspection NonAsciiCharacters */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpStaticAsDynamicMethodCallInspection */

declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\CreateTaskController;

use App\Domain\Task\CreateTaskService;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CreateTaskControllerTest extends TestCase
{
    public function test_method_invoke_call_service()
    {
        $service = $this->createMock(CreateTaskService::class);
        $request = $this->createStub(Request::class);
        $request->method('getParsedBody')
            ->willReturn(['title' => 'title1']);
        $response = $this->createStub(Response::class);
        $service->expects(self::once())
            ->method('serve');
        $controller = new CreateTaskController($service);
        call_user_func($controller, $request, $response, []);
    }
}
