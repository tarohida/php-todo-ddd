<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use App\Application\Actions\Task\ViewTaskAction;
use App\Application\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ViewTaskController implements HttpControllerInterface
{
    /**
     * @var ViewTaskAction
     */
    private ViewTaskAction $action;

    /**
     * ViewTaskController constructor.
     * @param ViewTaskAction $action
     */
    public function __construct(ViewTaskAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $id = (int)$args['id'];
            $task_data = $this->action->action($id);
            $content = sprintf("id:%s title:%s", $task_data->id(), $task_data->title());
            $response->getBody()->write($content);
            return $response;
        } catch (HttpNotFoundException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }
    }
}