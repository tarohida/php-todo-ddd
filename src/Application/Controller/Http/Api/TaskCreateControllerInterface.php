<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api;

use App\Application\Controller\Http\Api\Response\ValidationApiProblem;
use App\Application\Controller\Http\SlimHttpControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface TaskCreateControllerInterface
 * @package App\Application\Controller\Http\Api
 */
interface TaskCreateControllerInterface extends SlimHttpControllerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws ValidationApiProblem
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface;
}