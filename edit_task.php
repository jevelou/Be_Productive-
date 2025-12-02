<?php
session_start();
include 'db.php';

// Redirect to index.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch task details if task_id is provided
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $stmt = $conn->prepare("SELECT * FROM task WHERE task_id=? AND user_id=?");
    $stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $task_name = $row['task_name'];
        $task_description = $row['task_description'];
        $priority = $row['priority'];
        $deadline = $row['deadline'];
        $status = $row['status'];
        $category = $row['cat_id'];
    } else {
        echo "Task not found!";
        exit();
    }
}

// Update task if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['task_id']) && !empty($_POST['task_id']) &&
        isset($_POST['task_name']) && !empty($_POST['task_name']) &&
        isset($_POST['task_description']) &&
        isset($_POST['priority']) &&
        isset($_POST['deadline']) &&
        isset($_POST['status']) &&
        isset($_POST['category'])
    ) {
        // Sanitize input data
        $task_id = $_POST['task_id'];
        $task_name = $_POST['task_name'];
        $task_description = $_POST['task_description'];
        $priority = $_POST['priority'];
        $deadline = $_POST['deadline'];
        $status = $_POST['status'];
        $category_name = $_POST['category'];

        // Fetch cat_id based on category name
        $stmt = $conn->prepare("SELECT cat_id FROM category WHERE cat_name = ?");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $category = $row['cat_id'];
        } else {
            // Category does not exist, handle error or create new category
            echo "Error: Category not found.";
            exit();
        }

        // Prepare and bind parameters for updating task
        $update_stmt = $conn->prepare("UPDATE task SET task_name=?, task_description=?, priority=?, deadline=?, status=?, cat_id=? WHERE task_id=? AND user_id=?");
        $update_stmt->bind_param("ssssssii", $task_name, $task_description, $priority, $deadline, $status, $category, $task_id, $_SESSION['user_id']);

        // Execute the update statement
        if ($update_stmt->execute()) {
            // Task updated successfully
            header("Location: edit_task.php?task_id=$task_id&success=1");
            exit();
        } else {
            // Failed to update task
            header("Location: edit_task.php?task_id=$task_id&success=0");
            exit();
        }
    } else {
        echo "Missing required fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <title>Edit Task</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        background-color:  #81027b;
        background: linear-gradient(to right, #f755b3, #aa407e);
    }

    a {
        display: inline-block;
        margin-top: 20px;
        margin-left: 20px;
        text-decoration: none;
        border: 1px solid #ccc;
        padding: 10px 20px;
        border-radius: 5px;
        background-color: #014df1;
        color: #fff;
    }

    a:hover {
        background-color: #d63af5; /* Darken the color on hover */
        color: black;
    }

    .form-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 10px auto 50px;
        width: 70%;
        max-width: 500px;
        display: flex;
        flex-direction: column;
        position: relative; /* Maintain relative position */
        z-index: 1; /* Ensure it stays on top of images */
    }

    .left-image, .right-image {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 350px; /* Adjust the width as needed */
        height: 450px;
        z-index: 0; /* Ensure they stay behind the container */
    }

    .left-image {
        left: calc(50% - 22.5% - 330px); /* Adjust the position as needed */
    }

    .right-image {
        right: calc(50% - 22.5% - 330px); /* Adjust the position as needed */
    }

    .form-container h2 {
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-container label {
        display: block;
        font-size: 18px;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-container input[type="text"],
    .form-container textarea,
    .form-container select {
        width: calc(100% - 12px);
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 15px;
    }

    .form-container input[type="datetime-local"] {
        width: 100%;
        font-size: 15px;
    }

    .form-container button[type="submit"] {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        display: block;
        margin: 0 auto;
        text-align: center;
    }

    .form-container button[type="submit"]:hover {
        background-color: #0056b3;
    }   

    .error-message {
        color: #ff0000;
        font-size: 14px;
        margin-top: 5px;
    }
    </style>
</head>
<body>
    <!-- Your HTML content here -->
    <a href="home.php">Back to Task List</a>
    <img src="editleft_image.png" alt="Left Image" class="left-image"> 
    <div class="form-container" id="editTaskForm">
        <h2>Edit Task</h2>
        <?php
        if (isset($_GET['success'])) {
            if ($_GET['success'] == 1) {
                echo '<p style="color: green; text-align: center;">Task updated successfully!</p>';
            } elseif ($_GET['success'] == 0) {
                echo '<p style="color: red; text-align: center;">Failed to update task!</p>';
            }
        }
        ?>
        <form action="edit_task.php" method="POST">
            <input type="hidden" id="task_id" name="task_id" value="<?php echo $task_id; ?>">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" value="<?php echo $task_name; ?>" required><br>
            <label>Description:</label>
            <textarea name="task_description"><?php echo $task_description; ?></textarea><br>
            <label>Priority:</label>
            <select name="priority">
                <option value="High" <?php if($priority == 'High') echo 'selected'; ?>>High</option>
                <option value="Medium" <?php if($priority == 'Medium') echo 'selected'; ?>>Medium</option>
                <option value="Low" <?php if($priority == 'Low') echo 'selected'; ?>>Low</option>
            </select><br>
            <label>Deadline:</label>
            <input type="datetime-local" name="deadline" value="<?php echo date('Y-m-d\TH:i', strtotime($deadline)); ?>" required><br>
            <label>Status:</label>
            <select name="status">
                <option value="Pending" <?php if($status == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if($status == 'In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Completed" <?php if($status == 'Completed') echo 'selected'; ?>>Completed</option>
            </select><br>
            <label>Category:</label>
            <select name="category">
                <option value="Study" <?php if($category == 'Study') echo 'selected'; ?>>Study</option>
                <option value="Work" <?php if($category == 'Work') echo 'selected'; ?>>Work</option>
                <option value="Self" <?php if($category == 'Self') echo 'selected'; ?>>Self</option>
            </select><br>
            <button type="submit">Update Task</button>
        </form>
    </div>
    <img src="editright_image.png" alt="Right Image" class="right-image"> 

    <script src="script.js"></script>
</body>
</html>
