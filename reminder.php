<?php
// Start session at the very beginning
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: signin.php");
    exit();
}

include_once 'db.php';

// Retrieve user_id from session
$user_id = $_SESSION['user_id'];

// Function to retrieve tasks data
function getTasks($conn, $user_id) {
    $sql = "SELECT task_id, task_name, task_description, deadline, status FROM task WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $stmt->close();
    return $tasks;
}

// Function to retrieve goals data
function getGoals($conn, $user_id) {
    $sql = "SELECT goal_id, goal_name, goal_description, progress, goal_deadline FROM goal WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $goals = [];
    while ($row = $result->fetch_assoc()) {
        $goals[] = $row;
    }
    $stmt->close();
    return $goals;
}

// Retrieve data for the current user
$tasks = getTasks($conn, $user_id);
$goals = getGoals($conn, $user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">
    <title>Reminder</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Dancing Script', cursive;
            background-color:  #81027b;
            background: linear-gradient(to right, #f755b3, #aa407e);
        }
        h4 {
            text-align: center;
            align-items: center;
            margin: -20px 0;
            font-size: 50px;
            font-weight: 600;
        }
        main {
            width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #calendar {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        #event-details {
            width: 100%;
            max-width: 600px;
            background: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
            display: none;
            flex-direction: column;
            align-items: center;
        }
        #event-details h2 {
            margin-top: 0;
        }
        #event-details button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
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
            font-family: 'Open Sans', sans-serif;
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
    <a href="home.php">Back to Home</a>
    <h4>Reminder</h4>
    <main>
        <div id="calendar"></div>
        <div id="event-details" style="display:none;">
            <h2>Event Details</h2>
            <p id="event-title"></p>
            <p id="event-description"></p>
            <button onclick="document.getElementById('event-details').style.display='none'">Close</button>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    <?php foreach ($tasks as $task) { ?>
                        {
                            title: '<?php echo htmlspecialchars($task['task_name']); ?>',
                            start: '<?php echo $task['deadline']; ?>',
                            description: '<?php echo htmlspecialchars($task['task_description']); ?>',
                            backgroundColor: '#f755b3', // Task deadline color
                        },
                    <?php } ?>
                    <?php foreach ($goals as $goal) { ?>
                        {
                            title: '<?php echo htmlspecialchars($goal['goal_name']); ?>',
                            start: '<?php echo $goal['goal_deadline']; ?>',
                            description: '<?php echo htmlspecialchars($goal['goal_description']); ?>',
                            backgroundColor: '#aa407e', // Goal deadline color
                        },
                    <?php } ?>
                ],
                eventClick: function(info) {
                    var event = info.event;
                    document.getElementById('event-title').innerText = event.title;
                    document.getElementById('event-description').innerText = event.extendedProps.description;
                    document.getElementById('event-details').style.display = 'block';
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>