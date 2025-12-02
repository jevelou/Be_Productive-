<?php
// Start the session
session_start();

// Include database connection
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $update_query = "UPDATE users SET user_name = '$new_user_name', password = '$new_password' WHERE user_id = $user_id";
    if (mysqli_query($conn, $update_query)) {
        $message = "Profile updated successfully.";
        // Update session data
        $_SESSION['user_name'] = $new_user_name;
        $message_color = "green"; // Set message color to green for success
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
        $message_color = "red"; // Set message color to red for error
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    font-family: 'Open Sans', sans-serif;
    background-color:  #81027b;
    background: linear-gradient(to right, #f755b3, #aa407e);
}

.left-image, .right-image {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 0; /* Ensure they stay behind the container */
    }

    .left-image {
        left: calc(50% - 22.5% - 400px); /* Adjust the position as needed */
        width: 450px; /* Adjust the width as needed */
        height: 450px;
    }

    .right-image {
        right: calc(50% - 22.5% - 350px); /* Adjust the position as needed */
        width: 320px; /* Adjust the width as needed */
        height: 400px;
    }

.container {
    width: 50%;
    margin: 20px auto 50px;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    top: 100px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    align-items: center;
    position: relative; /* Maintain relative position */
    z-index: 1; /* Ensure it stays on top of images */
}

h2 {
    align-items: center;
    text-align: center;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
}

form div {
    margin-bottom: 15px;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 20px;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 20px;
}

button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #0056b3;
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

        /* Add this style for the message */
        .message {
            text-align: center;
            font-size: 18px;
            margin-top: 10px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>

</head>

<body>
<a href="home.php">Back to Profile</a>
<img src="profile_left_image.png" alt="Left Image" class="left-image"> 
    <div class="container">
    <h2>Edit Profile</h2>
    <?php if (!empty($message)) { ?>
    <p class="message <?php echo $message_color; ?>"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="edit_profile.php">
        <div>
            <label for="user_name">User Name:</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
        </div>
        <div>
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Update Profile</button>
    </form>
    </div>
    <img src="profiler_image.png" alt="Right Image" class="right-image"> 
    
</body>
</html>