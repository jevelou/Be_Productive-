<?php
session_start();
include_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "Error: User not logged in.";
        exit();
    }

    // Retrieve form data
    $user_id = $_SESSION['user_id'];
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    $category_name = $_POST['category']; // Retrieve category name

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

    // Prepare and execute SQL query to insert task
    $stmt = $conn->prepare("INSERT INTO task (user_id, task_name, task_description, priority, deadline, status, cat_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $user_id, $task_name, $task_description, $priority, $deadline, $status, $category);

    if ($stmt->execute() === TRUE) {
        // Task added successfully, redirect to the same page with success message in URL
        header("Location: add_task.php?message=Task added successfully.");
        exit();
    } else {
        // Failed to add task
        echo "Error: Failed to add task.";
    }

    $stmt->close();
} else {
    echo "Error: Invalid request.";
}

$conn->close();
?>
