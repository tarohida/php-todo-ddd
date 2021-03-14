<?php
declare(strict_types=1);


namespace App\Application\Controller\Http;


use App\Application\Actions\Task\DeleteTaskActionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteTaskController implements HttpControllerInterface
{
    /**
     * @var DeleteTaskActionInterface
     */
    private DeleteTaskActionInterface $action;

    public function __construct(DeleteTaskActionInterface $action)
    {
        $this->action = $action;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $task_id = filter_var($args['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $this->action->action($task_id);
        $response->getBody()->write(sprintf('task deleted. id: %s', $task_id));
        return $response;
    }
}