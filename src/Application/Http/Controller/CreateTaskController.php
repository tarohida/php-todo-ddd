<?php
declare(strict_types=1);

namespace App\Application\Http\Controller;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CreateTaskController implements SlimHttpControllerInterface
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $raw_result = [
            'task' => [
                1 => 'title1'
            ]
        ];
        $result = json_encode($raw_result);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException();
        }
        $response->getBody()->write($result);
        return $response;
    }
}