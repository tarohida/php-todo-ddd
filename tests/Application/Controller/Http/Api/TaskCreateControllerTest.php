<?php
declare(strict_types=1);

namespace Tests\Application\Controller\Http\Api;

use App\Application\Action\TaskCreateActionInterface;
use App\Application\Controller\Http\Api\Response\ValidationApiProblem;
use App\Application\Controller\Http\Api\TaskCreateController;
use App\Application\Controller\Http\Api\TaskCreateControllerInterface;
use App\Domain\Task\Exception\TaskValidateFailedWithTitleException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class TaskCreateControllerTest
 * @package Tests\Application\Controller\Http\Api
 *
 * 正常系はテストを実施していない。システムテストで確認する。
 */
class TaskCreateControllerTest extends TestCase
{
    private MockObject|TaskCreateActionInterface $action;
    private TaskCreateController $controller;
    private ServerRequestInterface|Stub $request;
    private ResponseInterface|Stub $response;
    private array $args;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = $this->createMock(TaskCreateActionInterface::class);
        $this->request = $this->createStub(ServerRequestInterface::class);
        $body = $this->createStub(StreamInterface::class);
        $this->response = $this->createStub(ResponseInterface::class);
        $this->response->method('getBody')
            ->willReturn($body);
        $this->args = [];
        $this->controller = new TaskCreateController($this->action);
    }

    public function test_implements_TaskCreateControllerInterface()
    {
        $this->assertInstanceOf(TaskCreateControllerInterface::class, $this->controller);
    }

    public function test_method_invoke_if_title_param_not_exists_then_throws_InvalidApiProblemException()
    {
        $controller = new TaskCreateController($this->action);
        $this->request->method('getParsedBody')
            ->willReturn([]);
        try {
            call_user_func($controller, $this->request, $this->response, $this->args);
            $this->fail('need to raise exception');
        } catch (ValidationApiProblem $exception) {
            $this->assertArrayHasKey('invalid_params', $exception->getExtensions());
        }
    }

    public function test_method_invoke_if_raise_TaskValidateFailedWithTitleException_throw_ValidationApiProblem()
    {
        $this->request->method('getParsedBody')
            ->willReturn(['title' => 'title1']);
        $exception = $this->createStub(TaskValidateFailedWithTitleException::class);
        $this->action->method('create')
            ->willThrowException($exception);
        $controller = new TaskCreateController($this->action);
        try {
            call_user_func($controller, $this->request, $this->response, $this->args);
            $this->fail('need to raise exception');
        } catch (ValidationApiProblem $exception) {
            $this->assertArrayHasKey('invalid_params', $exception->getExtensions());
        }
    }
}
