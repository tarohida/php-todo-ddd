<html lang="en">
<head>
    <title>delete task form</title>
</head>
<form>
    <label for="task_id">Task id: </label>
    <input type="text" id="task_id" />
</form>
<p id="content"></p>
<button type="button" onclick="deleteTask()">Delete task</button>

<script>
function deleteTask() {
    const id = document.getElementById("task_id").value;
    console.log(id);
    const xml_http = new XMLHttpRequest();
    xml_http.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE) {
            document.getElementById("content").innerHTML = this.responseText;
        }
    };
    xml_http.open("DELETE", "/tasks/" + id, true);
    xml_http.send();
}
</script>
</html>
