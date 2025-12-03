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

// Retrieve user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT user_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Function to retrieve tasks data
function getTasks($conn, $user_id) {
    $sql = "SELECT task_id, task_name, status FROM task WHERE user_id = ?";
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
    $sql = "SELECT goal_id, goal_name, progress FROM goal WHERE user_id = ?";
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
    <title>Productivity Dashboard</title>
</head>
<body>
    <div class="header-bar">
        <div class="logopic">
            <img src="belogo.png" alt="Logo Pic">
        </div>

        <h4 class="header-title">Welcome to Your Productivity Dashboard</h4>

        <!-- Profile container -->
        <div class="profile-container" onclick="toggleProfilePopup()">
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Profile popup -->
    <div class="profile-popup" id="profilePopup" style="display: none;">
        <h3><?php echo $user['user_name']; ?></h3>
        <a class="btn-profile" href="edit_profile.php">Edit Profile</a>
        <a class="btn-profile logout" href="logout.php">Logout</a>
    </div>

    <div class="transparent-container">
        <p>What would you like to do?</p>
        <div class="taskgoal-button">
            <li><button onclick="showTaskForm()">Add Task</button></li>
            <li><button onclick="showGoalForm()">Add Goal</button></li>
            <li><button onclick="redirectToLogTime()">Perform Task</button></li>
            <li><button onclick="redirectToProductivityChart()">View My Productivity Chart</button></li>
            <li><button onclick="redirectToReminder()">Reminder</button></li>
        </div>
    </div>

    <div class="listpic">
        <img src="checkpic.png" alt="Logo Pic">
    </div>

    <main>
        <!-- Task form section -->
        <div class="form-container" id="taskForm" style="display: none;">
            <h2>Task Actions</h2>
            <span class="exit-sign" onclick="hideForm('taskForm')">X</span>
            <form id="addTaskForm" onsubmit="addTask(event)">
                <label for="task_name">Task Name:</label>
                <input type="text" id="task_name" name="task_name" required><br>
                <label>Description:</label>
                <textarea name="task_description"></textarea><br>
                <label>Priority:</label>
                <select name="priority">
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select><br>
                <label>Deadline:</label>
                <input type="datetime-local" name="deadline"><br>
                <label>Status:</label>
                <select name="status">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select><br>
                <label>Category:</label>
                <select name="category">
                    <option value="Study">Study</option>
                    <option value="Work">Work</option>
                    <option value="Self">Self</option>
                </select><br>
                <button type="submit">Add Task</button>
            </form>
        </div>

        <div class="form-container" id="goalForm" style="display: none;">
            <h2>Goal Actions</h2>
            <span class="exit-sign" onclick="hideForm('goalForm')">X</span>
            <form id="addGoalForm" onsubmit="addGoal(event)">
                <label for="goal_name">Goal Name:</label>
                <input type="text" id="goal_name" name="goal_name" required><br>
                <label>Description:</label>
                <textarea name="goal_description"></textarea><br>
                <label>Deadline:</label>
                <input type="datetime-local" name="goal_deadline"><br>
                <label>Progress:</label>
                <input type="text" name="progress" placeholder="e.g., Percentage(10%)" required><br>
                <label>Category:</label>
                <select name="category">
                    <option value="Study">Study</option>
                    <option value="Work">Work</option>
                    <option value="Self">Self</option>
                </select><br>
                <button type="submit">Add Goal</button>
            </form>
        </div>

        <!-- Search bar section -->
        <div class="search_container">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" id="search" name="q" placeholder="Search task, goals, deadline, status..." required>
                <input type="submit" value="Search" class="button">
            </form>
        </div>

        <!-- For list of task -->
        <div class="container">
        <h2>List of Tasks</h2>
        <table id="tableList">
            <thead>
                <tr>
                    <th style="width: 10px;">ID</th>
                    <th style="width: 510px;">Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php
            include_once 'db.php';

            // Retrieve tasks from the database
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT task_id, task_name FROM task WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $i = 1;

            // Display the table with tasks
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $i++. "</td>";
                    echo "<td>" . $row['task_name'] . "</td>";
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

            $stmt->close();
            ?>
            </tbody>
        </table>
        </div>

        <!-- For list of goal -->
        <div class="container">
        <h2>List of Goals</h2>
        <table id="tableList">
            <thead>
                <tr>
                    <th style="width: 10px;">ID</th>
                    <th style="width: 510px;">Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php
            include_once 'db.php';

            // Retrieve goals from the database
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT goal_id, goal_name FROM goal WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $i = 1;

            // Display the table with goals
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $i++ . "</td>";
                    echo "<td>" . $row['goal_name'] . "</td>";
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

            $stmt->close();
            ?>
            </tbody>
        </table>
        </div>

        <!-- For list of Timelog -->
        <div class="container">
        <h2>List of Time Log</h2>
        <table id="tableList">
            <thead>
                <tr>
                    <th style="width: 10px;">ID</th>
                    <th style="width: 350px;">Title</th>
                    <th style="width: 40px;">Duration (minutes)</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php
            include_once 'db.php';

            // Retrieve timelog from the database
            $user_id = $_SESSION['user_id'];
            $sql = "
            SELECT tl.log_id, t.task_name, tl.duration
            FROM timelog tl
            JOIN task t ON tl.task_id = t.task_id
            WHERE t.user_id = ?";  
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $i = 1;

            // Display the table with time log
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $i++. "</td>";
                    echo "<td>" . $row['task_name'] . "</td>";
                    echo "<td>" . $row['duration'] . "</td>";
                    echo "<td>";
                    echo "<button class='btn btn-read' onclick='readTimeLog(" . $row['log_id'] . ")'>Read</button>";
                    echo "<button class='btn btn-delete' onclick='deleteTimeLog(" . $row['log_id'] . ")'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No time logs found.</td></tr>";
            }

                $stmt->close();
                ?>
            </tbody>
        </table>
        </div>

    </main>

    <div class="footer footer">
        <h3>About</h3>
        <p class="footer-text">
            This website helps you stay organized by tracking your tasks, goals, and productivity in a simple and motivating way.
        </p>
        
        <h3>Follow Us</h3>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
        </div>

        <div class="footer-bottom">
            <p>© 2025 Be_Productive! All rights reserved.</p>
        </div>
    </div>

<!-- Layout for Home page -->
<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body{
    font-family: 'Poppins', sans-serif;
    background-color:  #81027b;
    background: linear-gradient(to right, #f755b3, #aa407e);
    height: 100vh;
    display: flex;
    align-items: center;
    flex-direction: column;
    padding: 20px; /* Add padding to ensure space from edges */
    padding-top: 70px;
}

main {
    flex: 1; /* Allow the main content area to grow and fill remaining space */
}

.header-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100px;
    background: linear-gradient(to right, #f78bcf, #c052a0);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 40px;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.logopic {
    position: absolute;
    margin-right: 20px;
    left: -5px;
    top: -10px;
}

.logopic img {
    width: 280px; /* Set the maximum width of the image */
    height: 125px; /* Set the maximum height of the image */
}

.header-title {
    color: #fff;
    font-family: 'Dancing Script', cursive;
    font-size: 50px;
    text-shadow: 0 0 8px gray;
    text-align: center;
}

.profile-container {
    position: absolute;
    top: 20px;
    right: 40px;
    cursor: pointer;
}

.profile-container .fa-user-circle {
    font-size: 50px;
    color: #fff;
}

.profile-popup {
    position: fixed;
    top: 110px;
    right: 30px;
    width: 170px;
    background: white;
    border-radius: 15px;
    padding: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    display: none;
    z-index: 2000;
}

.profile-popup h3 {
    margin: 0 0 12px;
    text-align: center;
    font-size: 18px;
    color: #111;
}

.btn-profile {
    display: block;
    background: #be009f;
    color: white;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 8px;
    font-weight: 500;
}

.btn-profile:hover {
    background: #ff8be9;
    color: black;
}

.btn-profile.logout {
    background: #b5009d;
    color: white;
}

.btn-profile.logout:hover {
    background: #ff8be9;
    color: black;
}

.transparent-container {
    background-color: rgba(255, 255, 255, 0.5); /* Set the background color with opacity */
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 20px;
    position: fixed;
    left: 160px; /* Adjust as needed */
    top: 330px; /* Adjust as needed */
    transform: translate(-50%, -50%);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1; /* Ensure the container appears above other elements */
}

.transparent-container p {
    font-size: 18px; /* Adjust the font size */
    font-family: 'Poppins', sans-serif; /* Use the desired font family */
    color: black; /* Set the text color */
    font-weight: 600;
    margin-bottom: 15px; /* Adjust the bottom margin for spacing */
}

.taskgoal-button {
    position: relative; /* Change to relative positioning */
    z-index: 2; /* Ensure the buttons appear above the transparent container */
    align-items: center;
    margin-left: 25px;
}

.taskgoal-button button {
    margin: 3px 3px; /* Adjust the gap between buttons */
    margin-bottom: 15px;
    width: 190px; /* Change button width */
    height: 45px; /* Change button height */
    padding: 10px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

.taskgoal-button button:nth-child(1) {
    background-color: #0056c7; /* Change color for the first button */
    color: white; /* Change text color for the first button */
}

.taskgoal-button button:hover {
    background-color: #ffb5f4; /* Change color for the second button */
    color: black; /* Change text color for the second button */
}

.listpic {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    margin-right: 20px;
    right: -290px; /* Adjust as needed */
    top: 700px; /* Adjust as needed */
    transform: translate(-50%, -50%);
}

.listpic img {
    width: 400px; /* Set the maximum width of the image */
    height: 400px; /* Set the maximum height of the image */
}

.form-container {
    background-color: #CD0CD4;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 400px;
    text-align: center;
    position: fixed;
    top: 59%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    z-index: 100;
}

.form-container label {
    display: block;
    text-align: left;
    font-size: 20px;
    margin-bottom: 8px;
}

.form-container input,
.form-container textarea,
.form-container select {
    width: calc(80% - 40px); /* Adjusted width to align with labels */
    margin-bottom: 7px; /* Increased margin for better spacing */
    height: 32px;
}

.form-container input[type="submit"] {
    background-color: #007ef3ad; /* Change to your desired color */
    color: #fff;
    margin-top: 8px; /* Adjust spacing */
}

.form-container button {
    margin: 10px;
    padding: 10px 20px;
    background-color: #0056c7;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
}

.form-container button:hover {
    background-color: #ffb5f4;
    color: black;
}

.exit-sign {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
    z-index: 1000; /* Ensure the exit sign appears above other elements */
}   

.search_container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 50px;
    margin-left: 100px;
}

.search-form {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white;
    border-radius: 25px;
    padding: 12px;
    width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.search-form input[type="text"] {
    flex: 1;
    width: 300px;
    padding: 10px;
    border-radius: 25px;
    border: 1px solid #ddd;
    outline: none;
    transition: all 0.3s ease;
    font-size: 15px;
}

.search-form input[type="text"]:focus {
    border-color: #81027b;
    box-shadow: 0 0 5px rgba(129, 2, 123, 0.5);
}

.search-form input[type="submit"] {
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
    background-color: #0056c7;
    color: white;
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s, color 0.3s;
}

.search-form input[type="submit"]:hover {
    background-color: #5f075f80;
    color: #000;
}

.search-form svg {
    fill: none; /* Remove fill color */
    stroke: #000; /* Set stroke color to black*/
    width: 20px; /* Adjust the width of the SVG icon */
    height: 20px; /* Adjust the height of the SVG icon */
}

/* Styles for the containers */
.container {
    width: 700px;
    border-radius: 20px;
    padding: 20px;
    margin-top: 30px;
    margin-bottom: 30px;
    background-color: #fff;
    position: relative;
    top: 8px;
    left: 40px;
    overflow-x: auto;
    z-index: 1;
    color: #b5009d;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-family: 'Poppins', sans-serif;
}

/* Styles for the table */
.taskTable {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
th {
    background: #ff4fd6;
    color: white;
    width: 350px;
}
td {
    width: 150px;
}
.taskTableBody {
    text-align: left;
    width: 500px;
}
/* Styles for the buttons */
.btn {
    padding: 6px 10px;
    cursor: pointer;
    border: none;
    border-radius: 6px;
    width: 60px;
    height: 30px;
    font-size: 15px;
    color: white;
}
.btn-read {
    background-color: #4CAF50;
    margin-left: 8px;
}
.btn-edit {
    background-color: #2196F3;
    margin-left: 8px;
}
.btn-delete {
    background-color: #f44336;
    margin-left: 8px;
}
.btn-read:hover {
    background-color: #45a049; /* Darken the color on hover */
    color: black;
    opacity: 0.8;
}

.btn-edit:hover {
    background-color: #007a9b; /* Darken the color on hover */
    color: black;
    opacity: 0.8;
}

.btn-delete:hover {
    background-color: #d32f2f; /* Darken the color on hover */
    color: black;
    opacity: 0.8;
}

.footer.footer {
    height: auto;    
    width: 700px;
    background: rgba(255, 255, 255, 0.3); /* transparent like header */
    backdrop-filter: blur(5px);
    padding: 10px 10px;
    margin-top: 50px;
    border-radius: 20px;
    color: #ffffff;
    margin-left: 85px;
    margin-bottom: 20px;

    display: flex;
    flex-direction: column;     
    align-items: center;
    text-align: center;
}

.footer h3 {
    font-size: 21px;
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.footer-text {
    margin-top: 5px;      /* small spacing only */
    margin-bottom: 10px;
    line-height: 1.3;
    max-width: 650px;     /* keeps text neat, not stretched */
    font-size: 15px;
}

.footer-section p {
    font-size: 10px;
    margin: 0;
}

.social-links a {
    color: #ffffff;
    font-size: 22px;
    margin-top: 5px;
    margin-bottom: 10px;
    transition: 0.3s;
}

.social-links a:hover {
    opacity: 0.6;
}

.footer-bottom {
    text-align: center;
    margin-top: 10px;
    font-size: 15px;
    opacity: 0.8;
}

</style>

    <script src="script.js"></script>
</body>
</html>