<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Task\UpdateTaskCompletion;
use App\Domain\Task\Exception\TaskCompletedValidateException;
use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskId;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class UpdateTaskCompletionController implements SlimHttpControllerInterface
{
    public function __construct(private UpdateTaskCompletion $useCase)
    {
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        try {
            $id = TaskId::createFromMixedTypeValue($args['id'] ?? null);
            if (!is_array($body) || !array_key_exists('completed', $body)) {
                throw new TaskCompletedValidateException();
            }
            $completed = TaskCompleted::createFromMixedTypeValue($body['completed']);
        } catch (TaskIdValidateException|TaskCompletedValidateException) {
            throw new HttpBadRequestException($request);
        }

        try {
            $task = $this->useCase->execute($id, $completed);
        } catch (TaskNotFoundException) {
            throw new HttpNotFoundException($request);
        }

        $response->getBody()->write(json_encode(['task' => [
            'id' => $task->id(),
            'title' => $task->title(),
            'completed' => $task->completed(),
        ]], JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
