<?php
declare(strict_types=1);


namespace App\Application\Controller\Http;


use App\Application\Actions\Task\ListTaskAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListTaskController implements HttpControllerInterface
{
    /**
     * @var ListTaskAction
     */
    private ListTaskAction $action;

    public function __construct(ListTaskAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tasks = $this->action->action();
        if ($tasks->isEmpty()) {
            $response->getBody()->write('Task does not exists');
            return $response;
        }

        $stream = $response->getBody();
        foreach ($tasks as $task) {
            $task_id = $task->id();
            $task_title = $task->title();
            $stream->write(sprintf("id: %s; title: %s", $task_id, $task_title));
        }
        return $response;
    }
}