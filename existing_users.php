<?php
/*
 * CS22220 Group 3
 * Web booking system
 * existing_users.php developed by Nathan H. and David C.
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

//get user information
$sql_user = "SELECT first_name, surname, email, admin FROM users";
$result_user = mysqli_query($conn, $sql_user);

private_file_header($conn);
$admin = admin_file_header($conn, true);

if ($_POST['post_email']) {

    //checks if the user is trying to delete their own account
    function own_check($email) {
        if ($_SESSION['email'] == $email) {
            return true;
        } else {
            return false;
        }
    }

    $email = filter_input(INPUT_POST, 'post_email', FILTER_SANITIZE_EMAIL);
    if (own_check($conn)) {
        $state = 0;
    } elseif (!own_check($email)) {
        $sql = "DELETE FROM users WHERE email = '$email'";
        if (mysqli_query($conn, $sql)) {
            $state = 1;
        } else {
            $state = -1;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">

        <title>User list</title>
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

            <h2>Existing Users</h2>

            <div class="enclosure">
                <table>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Delete?</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_user)) {
                        if ($row['admin'] == 1) {
                            $type = 'Administrator';
                        } else {
                            $type = 'User';
                        }

                        echo '<tr>'
                        . '<td>' . $row['first_name'] . '</td>'
                        . '<td>' . $row['surname'] . '</td>'
                        . '<td>' . $row['email'] . '</td>'
                        . '<td>' . $type . '</td>';
                        echo '<td><form method="post" action="existing_users.php">'
                        . '<input type="hidden" name="post_email" value="' . $row['email'] . '">'
                        . '<input type = "submit" value = "Delete">'
                        . '</form></td>'
                        . '</tr>';
                    }
                    if ($_POST['post_email']) {
                        if ($state == 1) {
                            echo '<div class="success">User deleted!</div>';
                        } elseif ($state == 0) {
                            echo '<div class="error">You cannot delete your own account!</div>';
                        } elseif ($state == -1) {
                            echo '<div class="error">An error occured, please try again later!</div>';
                        }
                    }
                    ?>
                    <tr></tr>
                </table>
            </div>
        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>

    </body>
</html>