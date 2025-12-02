<?php
session_start(); // Start the session

include 'db.php'; // Include the file containing database connection details

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page
    header("Location: index.php");
    exit(); // Prevent further execution
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_goal_id"])) {
    // Retrieve goal ID from the form data
    $goal_id = $_POST["delete_goal_id"];

    // Prepare and bind the SQL statement to delete the goal
    $stmt = $conn->prepare("DELETE FROM goal WHERE goal_id = ?");
    $stmt->bind_param("i", $goal_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
    $conn->close();
    exit();
} else {
    echo "error: Invalid request";
}
?>
