<?php
session_start(); // Start the session

include 'db.php'; // Include the file containing database connection details

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page
    header("Location: index.php");
    exit(); // Prevent further execution
}

// Check if log_id is provided
if (!isset($_GET['log_id']) || empty($_GET['log_id'])) {
    // Redirect to the log time list page
    header("Location: home.php");
    exit();
}

$log_id = $_GET['log_id'];
$user_id = $_SESSION['user_id'];

// Fetch time log details
$stmt = $conn->prepare("
    SELECT tl.log_id, t.task_name, tl.start_time, tl.end_time, tl.duration 
    FROM timelog tl 
    JOIN task t ON tl.task_id = t.task_id 
    WHERE tl.log_id = ? AND t.user_id = ?");
$stmt->bind_param("ii", $log_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $task_name = $row['task_name'];
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];
    $duration = $row['duration'];
} else {
    // Log not found for the user
    echo "Log not found!";
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
    <title>Read Time Log</title>

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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #e91ade;
            color: #fff;
        }
    </style>
</head>
<body>
    <a href="home.php">Back to Task List</a>
    <img src="left_image.png" alt="Left Image" class="left-image"> 
    <div class="container">
    <h2>Time Log Details</h2>
        <p>Task Name: <span class="text"><?php echo $task_name; ?></span></p>
        <p>Start Time: <span class="text"><?php echo $start_time; ?></span></p>
        <p>End Time: <span class="text"><?php echo $end_time; ?></span></p>
        <p>Duration: <span class="text"><?php echo $duration; ?> minutes</span></p>
    </div>
    <img src="right_image.png" alt="Right Image" class="right-image">
</body>
</html>
