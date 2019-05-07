<?php
/*
 * CS22220 Group 3
 * Web booking system
 * pass_reset.php developed by David C.
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
        <title>Password change</title>
    </head>
    <body>

        <header>
            <h1><?php echo $website_name; ?></h1>
        </header>

        <div class="nav">
            <a id="account" href="login.php">Login</a>
        </div>

        <main>
            <?php
            $conn = mysqli_connect($host, $username, $password, $database);
            if (mysqli_connect_errno()) {
                echo "Database connection failed: " . mysqli_connect_error();
                die();
            }

            function valid($token, $conn) {
                $sql = "SELECT valid FROM pass_resets WHERE token = '$token'";
                $res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        return $row['valid'];
                    }
                } else {
                    return '-1';
                }
            }

            function passChange($conn, $token, $passIn) {
                $token_email = "SELECT email FROM pass_resets WHERE token = '$token'";
                $tok_res = mysqli_query($conn, $token_email);

                if (mysqli_num_rows($tok_res) > 0) {
                    while ($row = mysqli_fetch_assoc($tok_res)) {
                        $token_email = $row['email'];
                    }
                }

                $pass = password_hash($passIn, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = '$pass' WHERE email = '$token_email'";
                invalidate($token_email, $conn); //invalidates the token

                if (mysqli_query($conn, $sql)) {
                    return '1';
                } else {
                    return '0';
                }
            }

            if ($_GET) {
                $token = $_GET['token'];
                $validation = valid($token, $conn);
                if ($validation == '1') {
                    //echo "Token exists and is valid!<br>";
                    if (!empty($_POST['psw'])) {
                        $psw = $_POST['psw'];
                        $change = passChange($conn, $token, $psw);
                    }
                    /* if ($change == '1') {
                      echo "Password successfully changed!";
                      } elseif ($change == '0') {
                      echo "Change failed!";
                      } else {
                      echo "Please enter a new password!";
                      } */
                } elseif ($validation == '0') {//invalid
                    //echo "Token exists but is invalid!";
                    redirect('account.php', '302');
                } elseif ($validation == '-1') {//doesnt exist
                    //echo "Token doesn't exist!";
                    redirect('account.php', '302');
                } else {
                    echo "Something went wrong!";
                }
            } else {
                redirect('account.php', '302'); //no token redirect
            }
            ?>
            <h2>Please enter a new password</h2>
            <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                Password:<br>
                <input type="text" name="psw" required>
                <input type="submit" value="Submit"><br>
                <?php
                if ($change == '1') {
                    echo '<div class="success">Password successfully changed!</div>';
                } elseif ($change == '0') {
                    echo '<div class="error">Change failed!</div>';
                }
                ?>
            </form>
        </main>
        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>
    </body>
</html>