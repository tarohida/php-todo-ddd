<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api;


use App\Application\Controller\Http\Exception\FailedToParseJsonException;
use App\Domain\Task\TaskRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ListTaskApiController
 * @package App\Application\Controller\Http\Api
 */
class ListTaskApiController implements ListTaskApiControllerInterface
{
    public function __construct(
        private TaskRepositoryInterface $repository
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tasks = $this->repository->list();
        $payload = json_encode($tasks->getArray(), JSON_PRETTY_PRINT);
        $last_error_code = json_last_error();
        if ($last_error_code !== JSON_ERROR_NONE) {
            throw new FailedToParseJsonException($last_error_code, $tasks->getArray());
        }
        $response->getBody()->write($payload);
        return $response->withStatus(200);
    }
}