<?php
declare(strict_types=1);

namespace Tests\Application\Controller\Http\Api;

use App\Application\Action\TaskCreateActionInterface;
use App\Application\Controller\Http\Api\Response\ValidationApiProblem;
use App\Application\Controller\Http\Api\TaskCreateController;
use App\Application\Controller\Http\Api\TaskCreateControllerInterface;
use App\Application\Validation\ViolateParam\ViolateParamInterface;
use App\Application\Validation\ViolateParam\ViolateParamIteratorInterface;
use App\Domain\Task\Exception\TaskValidateException;
use Iterator;
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

    public function test_method_invoke_if_raise_TaskValidateException_throw_ValidationApiProblem()
    {
        $this->request->method('getParsedBody')
            ->willReturn(['title' => 'title1']);
        $violate_param = $this->createStub(ViolateParamInterface::class);
        $violate_param->method('getName')->willReturn('id');
        $violate_param->method('getReason')->willReturn('reason1');
        $violate_params_iterator = $this->createStub(ViolateParamIteratorInterface::class);
        $this->mockIterator($violate_params_iterator, [$violate_param]);
        $exception = $this->createStub(TaskValidateException::class);
        $exception->method('getViolateParams')
            ->willReturn($violate_params_iterator);
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

    /**
     * Mock iterator
     *
     * This attaches all the required expectations in the right order so that
     * our iterator will act like an iterator!
     * source from: http://www.davegardner.me.uk/blog/2011/03/04/mocking-iterator-with-phpunit/
     *
     * @param MockObject|Iterator $iterator The iterator object; this is what we attach
     *      all the expectations to
     * @param array An array of items that we will mock up, we will use the
     *      keys (if needed) and values of this array to return
     *      to "key"; only needed if you are doing foreach ($foo as $k => $v)
     *      as opposed to foreach ($foo as $v)
     * @author: dave@mpdconsulting.co.uk
     */
    private function mockIterator(
        MockObject|Iterator $iterator,
        array $items
    )
    {
        $iterator->expects($this->at(0))
            ->method('rewind');
        $counter = 1;
        foreach ($items as $v)
        {
            $iterator->expects($this->at($counter++))
                ->method('valid')
                ->will($this->returnValue(TRUE));
            $iterator->expects($this->at($counter++))
                ->method('current')
                ->will($this->returnValue($v));
            $iterator->expects($this->at($counter++))
                ->method('next');
        }
        $iterator->expects($this->at($counter))
            ->method('valid')
            ->will($this->returnValue(FALSE));
    }
}
