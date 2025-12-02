<?php
session_start();
include_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$search_query = isset($_GET['q']) ? '%' . $_GET['q'] . '%' : '';

$tasks_sql = "SELECT * FROM task WHERE user_id = ? AND (task_name LIKE ? OR task_description LIKE ? OR deadline LIKE ? OR status LIKE ?)";
$goals_sql = "SELECT * FROM goal WHERE user_id = ? AND (goal_name LIKE ? OR goal_description LIKE ? OR goal_deadline LIKE ? OR progress LIKE ?)";

$stmt_tasks = $conn->prepare($tasks_sql);
$stmt_tasks->bind_param("issss", $user_id, $search_query, $search_query, $search_query, $search_query);
$stmt_tasks->execute();
$result_tasks = $stmt_tasks->get_result();

$stmt_goals = $conn->prepare($goals_sql);
$stmt_goals->bind_param("issss", $user_id, $search_query, $search_query, $search_query, $search_query);
$stmt_goals->execute();
$result_goals = $stmt_goals->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>

<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background-color: #81027b;
    background: linear-gradient(to right, #f755b3, #aa407e);
}

h1 {
    text-align: center;
    align-items: center;
    margin-top: 10px;
    margin-bottom: 10px;
    top: 15px;
    left: 20px;
    z-index: 100;
    position: relative;
    text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
}

a {
    top: 70px; 
    left: 20px; 
    text-decoration: none;
    border: 1px solid #ccc;
    padding: 10px 20px;
    border-radius: 5px;
    background-color: #014df1;
    color: #fff;
    position: absolute;
}

a:hover {
    background-color: #d63af5; /* Darken the color on hover */
    color: black;
}

.left-image, .right-image {
        position: absolute;
        top: 60%;
        transform: translateY(-50%);
        z-index: 0; /* Ensure they stay behind the container */
    }

    .left-image {
        left: calc(50% - 22.5% - 350px); /* Adjust the position as needed */
        width: 310px; /* Adjust the width as needed */
        height: 450px;
    }

    .right-image {
        right: calc(50% - 22.5% - 350px); /* Adjust the position as needed */
        width: 300px; /* Adjust the width as needed */
        height: 490px;
    }

/* Styles for the containers */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 40px;
    position: relative;
    top: 30px;
}

.taskcontainer,
.goalcontainer {
    width: 700px;
    border-radius: 20px;
    padding: 20px;
    background-color: #fff;
    text-align: center;
    margin-bottom: 20px;
    top: 80px;
    position: relative;
}

/* Styles for the table */
.taskTable,
.goalTable {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
}

th,
td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #e91ade;
    width: 350px;
}

td {
    width: 150px;
}

/* Styles for the buttons */
.btn {
    padding: 5px 10px;
    cursor: pointer;
    border: none;
    border-radius: 3px;
    width: 60px;
    height: 30px;
    font-size: 15px;
}

.btn-read {
    background-color: #4caf50;
    color: white;
    margin-left: 10px;
}

.btn-edit {
    background-color: #008cba;
    color: white;
    margin-left: 10px;
}

.btn-delete {
    background-color: #f44336;
    color: white;
    margin-left: 10px;
}

.btn-read:hover {
    background-color: #45a049;
    /* Darken the color on hover */
    color: black;
}

.btn-edit:hover {
    background-color: #007a9b;
    /* Darken the color on hover */
    color: black;
}

.btn-delete:hover {
    background-color: #d32f2f;
    /* Darken the color on hover */
    color: black;
}
</style>
</head>
<body>
    <h1>Search Results</h1>
    <a href="home.php">Back to Home Page</a>
    <img src="searchleft_image.png" alt="Left Image" class="left-image"> 
    <div class="taskcontainer">
        <h2>List of Tasks</h2>
        <table class="taskTable">
            <thead>
                <tr>
                    <th style="width: 10px;">ID</th>
                    <th style="width: 510px;">Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_tasks->num_rows > 0) {
                    $i = 1;
                    while ($row = $result_tasks->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['task_name']) . "</td>";
                        echo "<td>";
                        echo "<button class='btn btn-read' onclick='redirectToReadTask(" . $row['task_id'] . ")'>Read</button>";
                        echo "<button class='btn btn-edit' onclick='editTask(" . $row['task_id'] . ")'>Edit</button>";
                        echo "<button class='btn btn-delete' onclick='deleteTask(" . $row['task_id'] . ")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No tasks found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="goalcontainer">
        <h2>List of Goals</h2>
        <table class="goalTable">
            <thead>
                <tr>
                    <th style="width: 10px;">ID</th>
                    <th style="width: 510px;">Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_goals->num_rows > 0) {
                    $i = 1;
                    while ($row = $result_goals->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['goal_name']) . "</td>";
                        echo "<td>";
                        echo "<button class='btn btn-read' onclick='redirectToReadGoal(" . $row['goal_id'] . ")'>Read</button>";
                        echo "<button class='btn btn-edit' onclick='editGoal(" . $row['goal_id'] . ")'>Edit</button>";
                        echo "<button class='btn btn-delete' onclick='deleteGoal(" . $row['goal_id'] . ")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No goals found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <img src="searchright_image.png" alt="Right Image" class="right-image"> 

    <script src="script.js"></script>
</body>
</html>

<?php
$stmt_tasks->close();
$stmt_goals->close();
$conn->close();
?>