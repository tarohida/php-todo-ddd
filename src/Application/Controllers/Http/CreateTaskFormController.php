<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateTaskFormController implements HttpControllerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = <<< HTML
<form action="/tasks/1" method="post">
<input type="text" name="title">
<input type="submit">
</form>
HTML;

        $response->getBody()->write($view);
        return $response;
    }
}