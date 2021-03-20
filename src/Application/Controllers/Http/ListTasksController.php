<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use App\Application\Actions\Task\ListTasksAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListTasksController implements HttpControllerInterface
{
    /**
     * @var ListTasksAction
     */
    private ListTasksAction $action;

    public function __construct(ListTasksAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tasks = $this->action->action();
        if ($tasks->isEmpty()) {
            $response->getBody()->write('Task does not exists');
            return $response;
        }

        $stream = $response->getBody();
        $style = <<< HTML
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
HTML;
        $view_open = <<< HTML
<table style="width:100%">
    <tr>
        <th>id</th>
        <th>title</th>
    </tr>
HTML;
        $view_close = <<< HTML
</table>
HTML;
        $stream->write($style);
        $stream->write($view_open);
        foreach ($tasks as $task) {
            $task_id = $task->id();
            $task_title = $task->title();
            $stream->write(sprintf("%s%s%s%s%s%s%s%s", '<tr>', '<th>', $task_id, '</th>', '<th>', $task_title, '</th>', '</tr>'));
        }
        $stream->write($view_close);
        return $response;
    }
}