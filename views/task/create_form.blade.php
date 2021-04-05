<html lang="en">
<head>
    <title>create task</title>
</head>
<body>
<form>
    <label for="task_id">Task id: </label><input type="text" id="task_id" /><br>
    <label for="task_title">Task title: </label><input type="text" id="task_title" /><br>
</form>
<p id="content"></p>
<button type="button" onclick="createTask()">Create task</button>
<script>
    function createTask() {
        const id = document.getElementById("task_id").value;
        const title = document.getElementById("task_title").value;
        const xml_http = new XMLHttpRequest();
        xml_http.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE) {
                document.getElementById("content").innerHTML = this.responseText;
            }
        };
        xml_http.open("POST", "/tasks/" + id, true);
        const form_data = new FormData();
        form_data.append('title', title);
        xml_http.send(form_data);
    }
</script>
</body>
</html>