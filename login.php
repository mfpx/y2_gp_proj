<?php
/*
 * CS22220 Group 3
 * Web booking system
 * login.php developed by David C. and Nathan H.
 */

session_start();

require 'includes/cfg.php'; //database config
require 'includes/lib.php'; //library
require 'includes/loginHandler.php'; //login handler
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//if email is set in session, redirect
if (isset($_SESSION['email'])) {
    redirect('existing_bookings.php', '302');
}

function init_session($email) {
    $_SESSION['email'] = $email;
    redirect('existing_bookings.php', '302');
}

if ($_POST['email'] && $_POST['password']) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // this doesnt need to be filtered, because its only passed to a validation function
    $pass = $_POST['password'];
    $valid = login($email, $pass, 'basic');
    if ($valid === true) {
        init_session($email);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">

        <title>Login</title>
    </head>
    <body>

        <header>
            <h1><?php echo $website_name; ?></h1>
        </header>

        <div class="nav">
            <a href="home.php">Home</a>
        </div>

        <main>
            <h4>Welcome to Group 3's Booking System</h4>
            <p>This site has been set up to show the system that we have
                designed for the CS22220 project</p>
            <form method="post" action="login.php">
                <div id="login">
                    Login
                </div>
                Email:<br>
                <input type="email" name="email" placeholder="username@example.com" required><br>
                Password:<br>
                <input type="password" name="password" placeholder="••••••••" required><br>
                <input type="submit" name="submit" value="Login"><br>
                <a href="reset.php">Forgotten password</a><br>
                <?php
                if ($valid === -1) {
                    echo '<div class="error">Wrong credentials!</div>';
                }
                ?>
            </form>

        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>

    </body>
</html>