<?php
// Include the db.php file
include 'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task_name'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $duration = $_POST['duration'];

    // Fetch task_id using task_name
    $stmt = $conn->prepare("SELECT task_id FROM task WHERE task_name = ?");
    $stmt->bind_param("s", $task_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $task_id = $row['task_id'];

        // Insert data into TimeLog table
        $sql = "INSERT INTO timeLog (task_id, start_time, end_time, duration) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($sql);
        $insert_stmt->bind_param("isss", $task_id, $start_time, $end_time, $duration);

        if ($insert_stmt->execute()) {
            $response = array("success" => true, "message" => "New record created successfully");
        } else {
            $response = array("success" => false, "message" => "Error: " . $sql . "<br>" . $conn->error);
        }
    } else {
        $response = array("success" => false, "message" => "Task not found!");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Log</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #81027b;
            background: linear-gradient(to right, #f755b3, #aa407e);
            overflow-x: hidden;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            margin-left: 20px;
            text-decoration: none;
            border: 1px solid #ccc;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #0056c7;
            color: #fff;
        }

        a:hover {
            background-color: #ffb5f4;
            color: black;
        }

        .container {
            max-width: 500px;
            margin: 50px auto 50px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            position: relative; /* Maintain relative position */
            z-index: 1; /* Ensure it stays on top of images */
        }

        .left-image, .right-image {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 330px; /* Adjust the width as needed */
        height: 410px;
        z-index: 0; /* Ensure they stay behind the container */
        }

        .left-image {
            left: calc(50% - 22.5% - 330px); /* Adjust the position as needed */
        }

        .right-image {
            right: calc(50% - 22.5% - 330px); /* Adjust the position as needed */
        }

        h2 {
            text-align: center;
            font-size: 35px;
            margin-bottom: 20px;
            margin-top: 10px;
            color: #333;
        }

        form {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            font-size: 20px;
        }

        form input[type="text"],
        form input[type="datetime-local"],
        form input[type="button"],
        form input[type="submit"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 18px;
        }

        form input[type="button"],
        form input[type="submit"] {
            background-color: #0056c7;
            color: white;
            cursor: pointer;
        }

        form input[type="button"]:hover,
        form input[type="submit"]:hover {
            background-color: #ffb5f4;
            color: black;
        }

        form input[type="hidden"] {
            display: none;
        }

        #timerDisplay {
        text-align: center;
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
        }
    </style>
    <script>
        var startTime;
        
        function startTimer() {
            startTime = new Date().getTime();
            document.getElementById('start_time').value = new Date().toISOString().slice(0, 19);
        }

        function stopTimer() {
            var endTime = new Date().getTime();
            var duration = (endTime - startTime) / 1000; // duration in seconds
            document.getElementById('duration').value = duration;
            document.getElementById('end_time').value = new Date().toISOString().slice(0, 19);
        }
    </script>
</head>
<body>
    <a href="home.php">Back to Home Page</a>
    <img src="logtime_left_image.png" alt="Left Image" class="left-image"> 
    <div class="container">
    <h2>Time Log</h2>
    <?php
        if (isset($response['message'])) {
            if ($response['success']) {
                echo '<p style="color: green; text-align: center;">' . $response['message'] . '</p>';
            } else {
                echo '<p style="color: red; text-align: center;">' . $response['message'] . '</p>';
            }
        }
    ?>
    <div id="timerDisplay">00:00:00</div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" required><br>
        <input type="hidden" id="start_time" name="start_time">
        <input type="hidden" id="end_time" name="end_time">
        <input type="hidden" id="duration" name="duration">
        <input type="button" id="startTimerBtn" value="Start Timer" onclick="startTimer()">
        <input type="button" value="Stop Timer" onclick="stopTimer()">
        <input type="submit" value="Submit">
    </form>
    </div>
    <img src="logtime_r_image.png" alt="Right Image" class="right-image"> 

    <script src="script.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
