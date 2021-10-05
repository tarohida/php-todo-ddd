<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DeleteTaskController implements SlimHttpControllerInterface
{

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $response;
    }
}