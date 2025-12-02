<?php
session_start();
include_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

     // Check if username or email already exists
    $stmt_check = $conn->prepare("SELECT user_name, email FROM Users WHERE user_name = ? OR email = ?");
    $stmt_check->bind_param("ss", $user_name, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    if ($result->num_rows > 0) {
        // Username or email already exists, redirect to signup with error message
        header("Location: index.php?error=3");
        exit();
    }
    $stmt_check->close();

    // Username and email are unique, proceed with inserting into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (user_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_name, $email, $hashed_password);

    if ($stmt->execute() === TRUE) {
        // After successful signup, set the user_id session variable
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $user_name;
        header("Location: home.php");
        exit();
    } else {
        // Handle error
        header("Location: signup.php?error=1");
        exit();
    }
}

$conn->close();
?>