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
    $goal_name = $_POST['goal_name'];
    $goal_description = $_POST['goal_description'];
    $deadline = $_POST['goal_deadline'];
    $progress = $_POST['progress'];
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

    // Prepare and execute SQL query to insert goal
    $stmt = $conn->prepare("INSERT INTO goal (user_id, goal_name, goal_description, goal_deadline, progress, cat_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssii", $user_id, $goal_name, $goal_description, $deadline, $progress, $category);

    if ($stmt->execute() === TRUE) {
        // Goal added successfully
        header("Location: home.php?message=Goal added successfully.");
        exit();
    } else {
        // Failed to add goal
        echo "Error: Failed to add goal.";
    }

    $stmt->close();
}

$conn->close();
?>