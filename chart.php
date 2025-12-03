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

// Fetch task data
$task_sql = "SELECT DATE(deadline) as date, status, COUNT(*) as count 
             FROM task 
             WHERE user_id = ? 
             GROUP BY DATE(deadline), status";
$stmt = $conn->prepare($task_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$task_result = $stmt->get_result();

$task_data = [];
while($row = $task_result->fetch_assoc()) {
    $task_data[] = $row;
}
$stmt->close();

// Fetch goal data
$goal_sql = "SELECT DATE(goal_deadline) as date, progress 
             FROM goal
             WHERE user_id = ?";
$stmt = $conn->prepare($goal_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$goal_result = $stmt->get_result();

$goal_data = [];
while($row = $goal_result->fetch_assoc()) {
    $goal_data[] = $row;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">
    <title>Productivity Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Dancing Script', cursive;
            background-color: #81027b;
            background: linear-gradient(to right, #f755b3, #aa407e);
        }

        /* Page title styling */
        h4 {
            text-align: center;
            align-items: center;
            margin: -20px 0;
            font-size: 50px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Back to Home link styling */
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
            font-family: 'Open Sans', sans-serif;
        }

        a:hover {
            background-color: #ffb5f4; /* Darken the color on hover */
            color: black;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        /* Chart container styling */
        .chart-container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Individual canvas styling */
        canvas {
            width: 100% !important;
            height: auto !important;
        }
    </style>
</head>
<body>
    <a href="home.php">Back to Home</a>
    <h4>Productivity Chart</h4>
    <div class="chart-container">
        <canvas id="taskChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="goalChart"></canvas>
    </div>

    <script>
        // Prepare task data for Chart.js
        const taskData = <?php echo json_encode($task_data); ?>;
        const goalData = <?php echo json_encode($goal_data); ?>;

        // Process task data
        const taskLabels = [...new Set(taskData.map(item => item.date))].sort();
        const taskStatuses = ['Pending', 'In Progress', 'Completed'];
        const taskStatusData = taskStatuses.map(status => {
            return taskLabels.map(label => {
                const task = taskData.find(item => item.date === label && item.status === status);
                return task ? task.count : 0;
            });
        });

        // Create task chart
        const ctxTask = document.getElementById('taskChart').getContext('2d');
        const taskChart = new Chart(ctxTask, {
            type: 'line',
            data: {
                labels: taskLabels,
                datasets: taskStatuses.map((status, index) => ({
                    label: status,
                    data: taskStatusData[index],
                    borderColor: ['red', 'yellow', 'green'][index],
                    fill: false
                }))
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Task Status Over Time'
                }
            }
        });

        // Process goal data
        const goalLabels = [...new Set(goalData.map(item => item.date))].sort();
        const goalProgressData = goalLabels.map(label => {
            const goal = goalData.find(item => item.date === label);
            return goal ? goal.progress : 0;
        });

        // Create goal chart
        const ctxGoal = document.getElementById('goalChart').getContext('2d');
        const goalChart = new Chart(ctxGoal, {
            type: 'line',
            data: {
                labels: goalLabels,
                datasets: [{
                    label: 'Goal Progress',
                    data: goalProgressData,
                    borderColor: 'blue',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Goal Progress Over Time'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10
                        },
                        title: {
                            display: true,
                            text: 'Progress (%)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>