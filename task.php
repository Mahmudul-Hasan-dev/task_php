<?php
// Define the path to the tasks file
$tasksFile = 'tasks.json';

// Load tasks from the JSON file
$tasks = [];
if (file_exists($tasksFile)) {
    $tasks = json_decode(file_get_contents($tasksFile), true);
}

// Handle form submission to add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $newTask = trim($_POST['task']);
    if (!empty($newTask)) {
        $tasks[] = ['task' => htmlspecialchars($newTask), 'done' => false];
        file_put_contents($tasksFile, json_encode($tasks));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle marking a task as done/undone
if (isset($_GET['mark'])) {
    $index = (int)$_GET['mark'];
    if (isset($tasks[$index])) {
        $tasks[$index]['done'] = !$tasks[$index]['done'];
        file_put_contents($tasksFile, json_encode($tasks));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle deleting a task
if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if (isset($tasks[$index])) {
        unset($tasks[$index]);
        $tasks = array_values($tasks); // Reindex the array
        file_put_contents($tasksFile, json_encode($tasks));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        .done {
            text-decoration: line-through;
            color: gray;
        }
        .task-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simple To-Do App</h1>
        <form method="POST">
            <input type="text" name="task" placeholder="Add a new task" required>
            <button type="submit">Add Task</button>
        </form>

        <div class="task-container">
            <h2>Tasks</h2>
            <ul>
                <?php foreach ($tasks as $index => $task): ?>
                    <li class="<?= $task['done'] ? 'done' : '' ?>">
                        <a href="?mark=<?= $index ?>"><?= htmlspecialchars($task['task']) ?></a>
                        <a href="?delete=<?= $index ?>" style="color: red; margin-left: 10px;">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
