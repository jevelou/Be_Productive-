<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>BeProductive! | Daily Productivity Tracker</title>
</head>

<body>  
<nav class="nav">
    <div class="nav-logo">
        <p>Your Daily Productivity Tracker! </p>
    </div>
</nav>
    <div class="picture-container">
        <img src="indexlogo.png" alt="Vertical Lockup on Blue Background">
    </div>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="signup.php" method="POST">
                <h2>Create Account</h2> <!---------- Sign Up ---------->
                <div class="social-icons">
                    <a href="https://www.google.com" class="icon"><i class="fa-brands fa-google"></i></a>
                    <a href="https://www.facebook.com" class="icon"><i class="fa-brands fa-facebook"></i></a>
                    <a href="https://www.twitter.com" class="icon"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://www.instagram.com" class="icon"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span1>or use your email for registeration</span1>
                <div class="error-message">
                <?php
                if (isset($_GET['error']) && $_GET['error'] == 3) {
                    echo "Username or email already exists";
                }
                ?>
                </div>
                <input type="text" name="user_name" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign Up</button>
                <div class="form-bottom">
                    <div class="one1">
                        <input type="checkbox" id="login-check">
                        <label for="login-check">Remember Me</label>
                    </div>
                    <div class="two1">
                        <label><a href="terms_and_conditions.php">Terms & Conditions</a></label>
                    </div>
                </div>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action = "signin.php" method = "POST"> 
                <h1>Sign In</h1> <!---------- Sign In ---------->
                <div class="social-icons">
                    <a href="https://www.google.com" class="icon"><i class="fa-brands fa-google"></i></a>
                    <a href="https://www.facebook.com" class="icon"><i class="fa-brands fa-facebook"></i></a>
                    <a href="https://www.twitter.com" class="icon"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://www.instagram.com" class="icon"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span2>or use your email password</span2>
                <div class="error-message">
                <?php
                    if (isset($_GET['error']) && $_GET['error'] == 1) {
                        echo "Wrong password";
                    } elseif (isset($_GET['error']) && $_GET['error'] == 3) {
                        echo "Email does not exist. Create an account.";
                    }
                ?>
                </div>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <div class="form-bottom">
                    <div class="one2">
                        <input type="checkbox" id="register-check">
                        <label for="register-check"> Remember Me</label>
                    </div>
                    <div class="two2">
                        <label><a href="#">Forgot Password?</a></label>
                    </div>
                </div>
                <button type="submit">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Hi, already have an account?</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Welcome, Ka-Productive!</h1>
                    <p>Don't have an account? Register to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>