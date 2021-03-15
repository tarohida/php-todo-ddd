<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use App\Application\Actions\Task\CreateTaskAction;
use App\Domain\Task\Task;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $parameters= $request->getParsedBody();
        $task_id = (int)$args['id'];
        $task_title = $parameters['title'];
        $task = new Task($task_id, $task_title);
        $this->action->action($task);
        $response->getBody()->write(sprintf('task created. id: %s, title: %s', $task_id, $task_title));
        return $response;
    }
}