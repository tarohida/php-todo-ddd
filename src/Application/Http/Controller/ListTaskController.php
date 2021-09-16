<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use App\Domain\Task\ListTaskService;
use App\Domain\Task\TaskRepositoryInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ListTaskController implements SlimHttpControllerInterface
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write('Hello world!');
        return $response;
    }
}