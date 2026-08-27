<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Application\Task\DeleteTask;
use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\TaskId;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DeleteTaskController implements SlimHttpControllerInterface
{

    /**
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $id = TaskId::createFromMixedTypeValue($args['id'] ?? null);
        } catch (TaskIdValidateException) {
            throw new HttpBadRequestException($request);
        }
        try {
            $this->useCase->execute($id);
        } catch (TaskNotFoundException) {
            throw new HttpNotFoundException($request);
        }
        return $response->withStatus(204);
    }

    public function __construct(
        private DeleteTask $useCase
    ) { }
}
