<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api;


use App\Application\Action\Exception\TaskAlreadyExistsException\TaskAlreadyExistsException;
use App\Application\Action\TaskCreateActionInterface;
use App\Application\Command\Task\TaskCreateCommand;
use App\Application\Controller\Http\Api\Response\ConflictApiProblem;
use App\Application\Controller\Http\Api\Response\ValidationApiProblem;
use App\Domain\Task\Exception\TaskValidateException;
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
     * @throws ConflictApiProblem
     * @throws ValidationApiProblem
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $parameter = $request->getParsedBody();
        if (!isset($args['id']) || !isset($parameter['title'])) {
            $extensions = [
                'invalid-params' => [
                    [
                        'name' => 'id',
                        'reason' => 'must be set'
                    ],
                    [
                        'name' => 'title',
                        'reason' => 'must be set'
                    ]
                ]
            ];
            throw new ValidationApiProblem('必須パラメータが不足しています。', $extensions);
        }
        if (!is_numeric($args['id'])) {
            $extensions = [
                'name' => 'id',
                'reason' => 'must be numeric'
            ];
            throw new ValidationApiProblem(
                'idの値は数値である必要があります',
                ['invalid-params' => $extensions]
            );
        }
        $command = new TaskCreateCommand((int)$args['id'], $parameter['title']);
        try {
            $this->action->create($command);
        } catch (TaskAlreadyExistsException $e) {
            $extensions = [
                'name' => 'id',
                'value' => $e->getTaskId()
            ];
            throw new ConflictApiProblem(
                '該当のタスクはすでに存在します。',
                ['conflicted' => $extensions]
            );
        } catch (TaskValidateException $e) {
            $invalid_params = [];
            foreach ($e->getViolateParams() as $violateParam) {
                $invalid_param = [];
                $invalid_param['name'] = $violateParam->getName();
                $invalid_param['reason'] = $violateParam->getReason();
                $invalid_params[] = $invalid_param;
            }
            throw new ValidationApiProblem(
                'Taskのパラメタが不正です',
                ['invalid-params' => $invalid_params]
            );
        }
        $return_params = [
            'id' => $command->id(),
            'title' => $command->title()
        ];
        $payload = json_encode($return_params, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);
        return $response->withStatus(200);
    }
}