<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api;


use App\Application\Action\TaskCreateActionInterface;
use App\Application\Command\Task\TaskCreateCommand;
use App\Application\Controller\Http\Api\Response\ValidationApiProblem;
use App\Domain\Task\Exception\TaskValidateFailedWithTitleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TaskCreateController
 * @package App\Application\Controller\Http\Api
 */
class TaskCreateController implements TaskCreateControllerInterface
{
    /**
     * TaskCreateController constructor.
     * @param TaskCreateActionInterface $action
     */
    public function __construct(
        private TaskCreateActionInterface $action
    ) {}

    /**
     * @throws ValidationApiProblem
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $parameter = $request->getParsedBody();
        if (!isset($parameter['title'])) {
            $extensions = [
                'invalid_params' => [
                    [
                        'name' => 'title',
                        'reason' => 'must be set'
                    ]
                ]
            ];
            throw new ValidationApiProblem('必須パラメータが不足しています。', $extensions);
        }
        $command = new TaskCreateCommand($parameter['title']);
        try {
            $task = $this->action->create($command);
        } catch (TaskValidateFailedWithTitleException) {
            $invalid_params = ['name' => 'title', 'reason' => 'Titleは1文字以上である必要があります'];
            throw new ValidationApiProblem(
                'Taskのパラメタが不正です',
                ['invalid_params' => $invalid_params]
            );
        }
        $return_params = [
            'id' => $task->id(),
            'title' => $task->title()
        ];
        $payload = json_encode($return_params, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);
        return $response->withStatus(200);
    }
}