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
<form>
    Task id: <input type="text" id="task_id" /><br>
    Task title: <input type="text" id="task_title" /><br>
</form>
<p id="content"></p>
<button type="button" onclick="createTask()">Create task</button>
<script>
function createTask() {
  const id = document.getElementById("task_id").value;
  const title = document.getElementById("task_title").value;
  const xml_http = new XMLHttpRequest();
  xml_http.onreadystatechange = function() {
    document.getElementById("content").innerHTML = this.responseText;
  };
  xml_http.open("POST", "/tasks/" + id, true);
  const form_data = new FormData();
  form_data.append('title', title);
  xml_http.send(form_data);
}
</script>
HTML;

        $response->getBody()->write($view);
        return $response;
    }
}