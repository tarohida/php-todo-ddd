<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use App\Application\Actions\Task\DeleteTaskActionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws HttpBadRequestException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $task_id = filter_var($args['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($task_id === false) {
            throw new HttpBadRequestException($request);
        }
        $this->action->action($task_id);
        $response->getBody()->write(sprintf('task deleted. id: %s', $task_id));
        return $response;
    }
}