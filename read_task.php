<?php
session_start(); // Start the session

include 'db.php'; // Include the file containing database connection details

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page
    header("Location: index.php");
    exit(); // Prevent further execution
}

// Check if task_id is provided
if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    // Redirect to the task list page
    header("Location: home.php");
    exit();
}

$task_id = $_GET['task_id'];
$user_id = $_SESSION['user_id'];

// Fetch task details
$stmt = $conn->prepare("CALL GetTaskDetails(?, ?)");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $task_name = $row['taskName'];
    $task_description = $row['taskDescription'];
    $priority = $row['taskPriority'];
    $deadline = $row['taskDeadline'];
    $status = $row['taskStatus'];
    $category = $row['taskCategory'];
} else {
    // Task not found for the user
    echo "Task not found!";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <title>Read Task</title>

    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Open Sans', sans-serif;
            background-color:  #81027b;
            background: linear-gradient(to right, #f755b3, #aa407e);
        }

        .container {
            width: 45%;
            margin: 20px auto 50px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            align-items: center;
            position: relative; /* Maintain relative position */
            z-index: 1; /* Ensure it stays on top of images */
        }

        .left-image, .right-image {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 300px; /* Adjust the width as needed */
            height: 300px;
            z-index: 0; /* Ensure they stay behind the container */
        }

        .left-image {
            left: calc(50% - 22.5% - 340px); /* Adjust the position as needed */
        }

        .right-image {
            right: calc(50% - 22.5% - 340px); /* Adjust the position as needed */
        }

        h2 {
            margin-bottom: 20px;
            font-size: 35px;
            text-align: center; /* Center align h2 */
            background-color: #e91ade;
            margin-top: 5px; /* Adjusted margin-top */
            border-radius: 10px;
        }

        p {
            margin-bottom: 10px;
            font-size: 25px; /* Adjust font size */
            font-weight: bold; /* Make text bold */
            display: flex;
            justify-content: space-between;
        }

        .text {
            font-size: 20px;
            flex: 1;
            text-align: center;
            margin-left: 10px;
            font-weight: normal;
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

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>
    <a href="home.php">Back to Task List</a>
    <img src="left_image.png" alt="Left Image" class="left-image"> 
    <div class="container">
        <h2>Task Details</h2>
        <p>Task Name: <span class="text"><?php echo $task_name; ?></span></p>
        <p>Description: <span class="text"><?php echo $task_description; ?></span></p>
        <p>Priority: <span class="text"><?php echo $priority; ?></span></p>
        <p>Deadline: <span class="text"><?php echo $deadline; ?></span></p>
        <p>Status: <span class="text"><?php echo $status; ?></span></p>
        <p>Category: <span class="text"><?php echo $category; ?></span></p>
    </div>
    <img src="right_image.png" alt="Right Image" class="right-image">
</body>
</html>