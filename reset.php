<?php
/*
 * CS22220 Group 3
 * Web booking system
 * reset.php developed by David C. and Nathan H.
 */


require 'includes/cfg.php'; //config
require 'includes/lib.php'; //library
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">
        <title>Password reset</title>
    </head>
    <body>
        <header>
            <h1><?php echo $website_name; ?></h1>
        </header>

        <div class="nav">
            <a id="account" href="login.php">Login</a>
        </div>
        <main>
            <h2>Password reset</h2>
            <form method="POST" action="reset.php">
                Email: <br>
                <input type="text" name="email" required><br>
                <input type="submit" value="Submit"><br>
                <?php
                if ($_POST) {
                    echo '<div class="success">If the email is correct, you should receive an email!</div>';
                }
                ?>
            </form>
            <?php
            $conn = mysqli_connect($host, $username, $password, $database);
            if (mysqli_connect_errno()) {
                echo "Database connection failed: " . mysqli_connect_error();
                die();
            }

            if ($_POST) {
                $email = $_POST['email'];
                invalidate($email, $conn); //invalidates previous requests

                $check = "SELECT email FROM users WHERE email = '$email'";
                $res = mysqli_query($conn, $check);

                if (mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $db_email = $row['email'];
                        if ($email == $db_email) {
                            $date = date("Y-m-d"); //eg 2019-01-18
                            $time = date("H:i:s"); //eg 03:47:59 (24hr clock)
                            $ip = getClientIP();
                            $token = token(16); //generates a random 32-char token
                            $sql = "INSERT INTO pass_resets VALUES ('$db_email', '$ip', '$date', '$time', '$token', TRUE)";

                            mysqli_query($conn, $sql);
                            email($email, $token);
                        }
                    }
                }
            }
            ?>
        </main>
    </body>
</html>