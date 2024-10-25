<?php
session_start(); // Start the session
include 'db.php';

// Initialize message and error variables
$message = '';
$error = '';

// Add Task
if (isset($_POST['add'])) {
    $task = $_POST['task'];
    $due_date = $_POST['due_date'];

    // Validate due date
    if (strtotime($due_date) <= time()) {
        $error = "Due date must be a future date.";
    } else {
        $sql = "INSERT INTO tasks (task, due_date) VALUES ('$task', '$due_date')";
        if (mysqli_query($conn, $sql)) {
            $message = "Task added successfully!";
        } else {
            $error = "Error adding task.";
        }
    }
}

// Mark Task as Complete
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    if (mysqli_query($conn, "UPDATE tasks SET status='completed' WHERE id=$id")) {
        $message = "Task marked as complete!";
        header("Location: index.php"); // Redirect to avoid resubmission
        exit; // Stop further script execution
    }
}

// Delete Task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM tasks WHERE id=$id")) {
        $message = "Task deleted successfully!";
        header("Location: index.php"); // Redirect to avoid resubmission
        exit; // Stop further script execution
    }
}

// Fetch Tasks
$result = mysqli_query($conn, "SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Function to show alert messages
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <h1>To-Do List</h1>

    <form action="" method="POST">
        <input type="text" name="task" placeholder="Enter your task" required>
        <input type="date" name="due_date" required>
        <button type="submit" name="add">Add Task</button>
    </form>

    <h2>Tasks</h2>
    <table>
        <tr>
            <th>Task</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($task = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($task['task']); ?></td>
            <td><?php echo htmlspecialchars($task['due_date']); ?></td>
            <td><?php echo htmlspecialchars($task['status']); ?></td>
            <td>
                <a href="?complete=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to mark this task as complete?');">Complete</a>
                <a href="?delete=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <script>
        // Show alert messages for add and delete actions
        <?php if ($message) echo "showAlert('$message');"; ?>
        <?php if ($error) echo "showAlert('$error');"; ?>
    </script>
</body>
</html>
