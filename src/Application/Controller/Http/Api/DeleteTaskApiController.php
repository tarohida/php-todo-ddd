<?php
declare(strict_types=1);

namespace App\Application\Controller\Http\Api;

use App\Application\Controller\Http\Api\Response\ValidationApiProblem;
use App\Domain\Task\DeleteTaskService;
use App\Domain\Task\Exception\Validate\TaskIdMustBePositiveNumberException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

/**
 * Class TaskDeleteApiController
 * @package App\Application\Controller\Http\Api
 */
class DeleteTaskApiController implements DeleteTaskApiControllerInterface
{
    /**
     * TaskDeleteApiController constructor.
     */
    public function __construct(
        private DeleteTaskService $service
    ) { }

    /**
     * @throws HttpNotFoundException
     * @throws ValidationApiProblem
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!isset($args['id']) || !is_numeric($args['id'])) {
            throw new HttpNotFoundException($request);
        }
        try {
            $this->service->delete((int)$args['id']);
        } catch (TaskIdMustBePositiveNumberException) {
            $extensions = [
                'invalid-params' => [
                    'name' => 'id',
                    'reason' => '1以上の整数である必要があります'
                ]
            ];
            throw new ValidationApiProblem('Taskのパラメタが不正です', $extensions);
        }
        return $response->withStatus(200);
    }
}