<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Domain\Task\Exception\TaskIdValidateException;
use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
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
        $rawId = $args['id'] ?? null;
        if (!is_string($rawId) || preg_match('/^[1-9][0-9]*$/D', $rawId) !== 1 || !$this->fitsInInteger($rawId)) {
            throw new HttpBadRequestException($request);
        }
        try {
            $id = TaskId::createFromMixedTypeValue($rawId);
        } catch (TaskIdValidateException) {
            throw new HttpBadRequestException($request);
        }
        try {
            $this->repository->delete($id);
        } catch (TaskNotFoundException) {
            throw new HttpNotFoundException($request);
        }
        return $response->withStatus(204);
    }

    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }

    private function fitsInInteger(string $id): bool
    {
        $maximum = (string) PHP_INT_MAX;
        return strlen($id) < strlen($maximum)
            || (strlen($id) === strlen($maximum) && strcmp($id, $maximum) <= 0);
    }
}
