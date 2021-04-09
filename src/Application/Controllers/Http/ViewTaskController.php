<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use App\Application\Actions\Task\ViewTaskAction;
use App\Exeption\Application\Actions\Task\SpecifiedTaskNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class ViewTaskController implements ViewTaskControllerInterface
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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws HttpBadRequestException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $id = filter_var($args['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false) {
                throw new HttpBadRequestException($request);
            }
            $task_data = $this->action->action($id);
            $content = sprintf("id:%s title:%s", $task_data->id(), $task_data->title());
            $response->getBody()->write($content);
            return $response;
        } catch (SpecifiedTaskNotFoundException $e) {
            $response->getBody()->write(sprintf('Specified task not found: %s', $e->getTaskId()));
            return $response->withStatus(404);
        }
    }
}