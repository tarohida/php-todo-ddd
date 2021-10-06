<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DeleteTaskController implements SlimHttpControllerInterface
{

    /**
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id'])) {
            throw new HttpBadRequestException($request);
        }
        try {
            $id = TaskId::createFromRawParam($args['id']);
        } catch (TaskIdValidateException) {
            throw new HttpBadRequestException($request);
        }
        $this->repository->delete($id);
        return $response->withStatus(200);
    }

    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }
}