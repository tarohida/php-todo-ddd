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
<button type="button" onclick="deleteTask()">Delete task</button>
<p id="content"></p>
<form>
    <input type="text" id="task_id" />
</form>

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