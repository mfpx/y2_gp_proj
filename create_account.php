<?php
/*
 * CS22220 Group 3
 * Web booking system
 * create_account.php developed by Nathan H. and David C.
 */

session_start();

require 'includes/cfg.php'; //config
require 'includes/lib.php'; //library
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

private_file_header($conn);
$admin = admin_file_header($conn, true);

//name and email are the most obvious vars to check
if (isset($_POST['firstName']) && isset($_POST['email'])) {
    $name = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $user_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'tempPassword', FILTER_SANITIZE_STRING);
    $repassword = filter_input(INPUT_POST, 'reTempPassword', FILTER_SANITIZE_STRING);
    $utype = filter_input(INPUT_POST, 'userClass', FILTER_SANITIZE_NUMBER_INT);
    $enc_pass = password_hash($password, PASSWORD_DEFAULT);

    function exists($email, $conn) {
        $sql = "SELECT email FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    if ($password === $repassword) {
        if (!exists($user_email, $conn)) {
            $sql = "INSERT INTO users VALUES('$name','$surname','$user_email','$enc_pass','$utype')";
            if (mysqli_query($conn, $sql)) {
                $state = 1;
            } else {
                $state = -1;
            }
        } else {
            $state = -2;
        }
    } else {
        $state = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">

        <title>Account creation</title>
    </head>
    <body>

        <header>
            <h1><?php echo $website_name; ?></h1>
        </header>

        <div class="nav">
            <a href="home.php">Home</a>
            <div class="dropdown">
                <button class="dropdownBtn">Locations</button>
                <div class="dropdown-content">
                    <a href="available_rooms.php">Available Rooms</a>
                    <a href="available_cats.php">Available Categories</a>
                    <?php
                    if ($admin == true) {
                        echo '<a href = "add_room.php">Add Room</a>';
                        echo '<a href="add_cat.php">Add Category</a>';
                    }
                    ?>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropdownBtn">Bookings</button>
                <div class="dropdown-content">
                    <a href="existing_bookings.php">Existing Bookings</a>
                    <a href="create_booking.php">Create Booking</a>
                </div>
            </div>
            <a id="account" href="account.php">Your Account</a>
        </div>

        <main>

            <h2>Create An Account</h2>

            <div class="form">
                <form id="createAccountForm" action="create_account.php" method="post">
                    <h3>First Name:</h3>
                    <input type="text" required
                           name="firstName" placeholder="John"
                           oninvalid="this.setCustomValidity('Please enter a First Name')"
                           oninput="this.setCustomValidity('')">
                    <h3>Surname:</h3>
                    <input type="text" required
                           name="surname" placeholder="Locke"
                           oninvalid="this.setCustomValidity('Please enter a Surname')"
                           oninput="this.setCustomValidity('')">
                    <h3>Email:</h3>
                    <input type="email" required
                           name="email" placeholder="user@example.com"
                           oninvalid="this.setCustomValidity('Please enter a valid Email')"
                           oninput="this.setCustomValidity('')">
                    <h3>Temporary Password:</h3>
                    <input type="password" required
                           name="tempPassword" placeholder="••••••••"
                           oninvalid="this.setCustomValidity('Please enter a Password')"
                           oninput="this.setCustomValidity('')">
                    <h3>Re-enter Temporary Password:</h3>
                    <input type="password" required
                           name="reTempPassword" placeholder="••••••••"
                           oninvalid="this.setCustomValidity('Please re-enter the Password')"
                           oninput="this.setCustomValidity('')">
                    <h3>User Class:</h3>
                    <select required
                            name="userClass" placeholder="Select"
                            oninvalid="this.setCustomValidity('Please select a User Class')"
                            oninput="this.setCustomValidity('')">>
                        <option value="0">Standard User</option>
                        <option value="1">Admin</option>
                    </select><br>
                    <input type="submit" name="submit" value="Create">
                    <?php
                    if ($state == 1 && $_POST) {
                        echo '<div class="success">Account created successfully!</div>';
                    } elseif ($state == 0 && $_POST) {
                        echo '<div class="error">Passwords don\'t match!</div>';
                    } elseif ($state == -2 && $_POST) {
                        echo '<div class="error">User with that email exists!</div>';
                    } elseif ($state == -1 && $_POST) {
                        echo '<div class="error">User creation failed, plase try again later!</div>';
                    }
                    ?>
                </form>
            </div>
        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>

    </body>
</html>