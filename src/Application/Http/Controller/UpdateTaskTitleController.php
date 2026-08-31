<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Http\Controller\Exception\JsonConvertFailedException;
use App\Application\Task\UpdateTaskTitle;
use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Exception\TaskTitleValidateException;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class UpdateTaskTitleController implements SlimHttpControllerInterface
{
    public function __construct(private UpdateTaskTitle $useCase) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        if (!is_array($body)) {
            throw new HttpBadRequestException($request);
        }
        try {
            $id = TaskId::createFromMixedTypeValue($args['id'] ?? null);
            $title = TaskTitle::createFromMixedTypeValue($body['title'] ?? null);
        } catch (TaskIdValidateException|TaskTitleValidateException) {
            throw new HttpBadRequestException($request);
        }
        try {
            $task = $this->useCase->execute($id, $title);
        } catch (TaskNotFoundException) {
            throw new HttpNotFoundException($request);
        }
        $rawResult = ['task' => [
            'id' => $task->id(),
            'title' => $task->title(),
            'completed' => $task->completed(),
        ]];
        $result = json_encode($rawResult);
        if ($result === false) {
            throw new JsonConvertFailedException(params: $rawResult);
        }
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
