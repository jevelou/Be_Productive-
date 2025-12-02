<?php
session_start(); // Start the session
include_once 'db.php'; // Include the file containing database connection details

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page or handle the session expiration
    header("Location: index.php");
    exit(); // Prevent further execution
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_log_id"])) {
    // Retrieve log ID from the form data
    $log_id = $_POST["delete_log_id"];

    // Prepare and bind the SQL statement to delete the time log
    $stmt = $conn->prepare("DELETE FROM timelog WHERE log_id = ?");
    $stmt->bind_param("i", $log_id);

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