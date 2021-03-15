<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use App\Application\Actions\Task\CreateTaskAction;
use App\Domain\Task\Task;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class CreateTaskController implements HttpControllerInterface
{
    /**
     * @var CreateTaskAction
     */
    private CreateTaskAction $action;

    public function __construct(CreateTaskAction $action)
    {
        $this->action = $action;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws HttpBadRequestException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $parameters= $request->getParsedBody();
        $task_id = filter_var($args['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($task_id === false) {
            throw new HttpBadRequestException($request);
        }
        $task_title = $parameters['title'];
        $task = new Task($task_id, $task_title);
        $this->action->action($task);
        $response->getBody()->write(sprintf('task created. id: %s, title: %s', $task_id, $task_title));
        return $response;
    }
}