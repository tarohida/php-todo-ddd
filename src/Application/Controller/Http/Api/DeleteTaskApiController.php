<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TaskDeleteApiController
 * @package App\Application\Controller\Http\Api
 */
class DeleteTaskApiController implements DeleteTaskApiControllerInterface
{
    /**
     * TaskDeleteApiController constructor.
     */
    public function __construct()
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response->withStatus(200);
    }
}