<?php
declare(strict_types=1);

namespace Tests\Application\Http\Controller;

use App\Application\Http\Controller\ListTaskController;
use App\Application\Task\ListTasks;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskTitle;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

final class ListTaskControllerTest extends TestCase
{
    public function test_returns_use_case_result_as_api_contract_json(): void
    {
        $useCase = $this->createMock(ListTasks::class);
        $useCase->expects(self::once())->method('execute')->willReturn(new TaskList([
            new Task(
                TaskId::createFromMixedTypeValue(1),
                TaskTitle::createFromMixedTypeValue('Write tests'),
            ),
        ]));
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/tasks');

        $response = (new ListTaskController($useCase))($request, new Response(), []);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            [['id' => 1, 'title' => 'Write tests', 'completed' => false]],
            json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
    }
}
