<?php
declare(strict_types=1);


namespace App\Application\Controller\Http;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface HttpControllerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface;
}