<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteTaskFormController implements HttpControllerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = <<< HTML
<form>
    Task id: <input type="text" id="task_id" />
</form>
<p id="content"></p>
<button type="button" onclick="deleteTask()">Delete task</button>

<script>
function deleteTask() {
  const id = document.getElementById("task_id").value;
  const xml_http = new XMLHttpRequest();
  xml_http.onreadystatechange = function() {
    document.getElementById("content").innerHTML = this.responseText;
  };
  xml_http.open("DELETE", "/tasks/" + id, true);
  xml_http.send();
}
</script>
HTML;

        $response->getBody()->write($view);
        return $response;
    }
}