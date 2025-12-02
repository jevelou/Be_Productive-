<?php
session_start();
include_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM Users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // After successful sign in, set the user_id session variable
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            header("Location: home.php");
            exit();
        } else {
            // Incorrect password
            header("Location: index.php?error=1");
            exit();
        }
    } elseif ($result->num_rows == 0) {
        // Email not found
        header("Location: index.php?error=3");
        exit();
    } else {
        // Multiple users with the same email (should not happen)
        header("Location: signin.php?error=Email is not existing. Create an account first.");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>