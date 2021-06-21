<?php
declare(strict_types=1);


namespace App\Application\Controller\Http;


use App\Application\Controller\Http\Exception\FailedToRenderHtmlException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ListTaskController
 * @package App\Application\Controller\Http\Form
 */
class ListTaskController implements ListTaskControllerInterface
{
    public function __construct(
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $content = file_get_contents(PROJECT_ROOT_PATH . '/views/list_task.html');
        if ($content === false) {
            throw new FailedToRenderHtmlException('htmlの取得に失敗');
        }
        $response->getBody()->write($content);
        return $response->withStatus(200);
    }
}