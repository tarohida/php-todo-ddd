<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Http\Controller\Exception\JsonConvertFailedException;
use App\Domain\Task\CreateTaskService;
use App\Domain\Task\Exception\TaskTitleValidateException;
use App\Domain\Task\TaskTitle;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CreateTaskController implements SlimHttpControllerInterface
{
    /**
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $title = TaskTitle::createFromRowParam($request->getParsedBody()['title'] ?? null);
        } catch (TaskTitleValidateException) {
            throw new HttpBadRequestException($request);
        }
        $task = $this->service->serve($title);
        $raw_result = [
            'task' => [
                $task->id() => $task->title()
            ]
        ];
        $result = json_encode($raw_result);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonConvertFailedException(params: $raw_result);
        }
        $response->getBody()->write($result);
        return $response;
    }

    public function __construct(
        private CreateTaskService $service
    ) {}
}