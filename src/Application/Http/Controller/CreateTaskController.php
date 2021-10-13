<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Domain\Task\CreateTaskService;
use App\Domain\Task\Exception\TaskTitleValidateException;
use App\Domain\Task\TaskTitle;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CreateTaskController implements SlimHttpControllerInterface
{
    /**
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $request_body = $request->getParsedBody();
        try {
            $title = TaskTitle::createFromRowParam($request_body['title'] ?? null);
        } catch (TaskTitleValidateException) {
            throw new HttpBadRequestException($request);
        }
        $this->service->serve($title);
        $raw_result = [
            'task' => [
                1 => $title->title()
            ]
        ];
        $result = json_encode($raw_result);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException();
        }
        $response->getBody()->write($result);
        return $response;
    }

    public function __construct(
        private CreateTaskService $service
    ) {}
}