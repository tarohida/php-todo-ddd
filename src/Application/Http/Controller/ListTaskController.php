<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Http\Controller\Exception\JsonConvertFailedException;
use App\Domain\Task\ListTaskService;
use App\Domain\Task\TaskList;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ListTaskController implements SlimHttpControllerInterface
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write($this->dumpJson($this->service->list()));
        return $response;
    }

    public function __construct(
        private ListTaskService $service
    ) { }

    private function dumpJson(TaskList $tasks): bool|string
    {
        $raw_result = [];
        foreach ($tasks as $task) {
            $raw_result[] = [
                'id' => $task->id(),
                'title' => $task->title()
            ];
        }
        $result = json_encode($raw_result);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonConvertFailedException(params: $raw_result);
        }
        return $result;
    }
}