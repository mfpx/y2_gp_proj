<?php
/*
 * CS22220 Group 3
 * Web booking system
 * account.php developed by David C., Nathan H.
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

//this SHOULD prevent the user from doing anything unless logged in
if (!isset($_SESSION['email'])) {
    redirect('login.php', '302');
}

//gets the user data
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($email !== $row['email']) {
    logout('login.php', '302');
}

//user variables
$fname = $row['first_name'];
$lname = $row['surname'];
$admin = $row['admin'];
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">

        <title>Account</title>
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
            <?php
            if ($admin == true && $syserrors == true) {
                if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
                    echo '<div class="error">Warning: You are not using SSL!</div><br>';
                }
            }
            ?>
            <h3>
                Welcome, <?php echo $fname; ?>!
            </h3>
            Name: <?php echo $fname . ' ' . $lname; ?><br>
            Email: <?php echo $_SESSION['email']; ?><br>
            Account type: 
            <?php
            if ($admin == true) {
                echo 'Administrator';
            } elseif ($admin == false) {
                echo 'User';
            }
            ?><br>
            <?php
            if ($admin == true) {
                echo '<br><a href = "create_account.php">Create account</a><br>';
                echo '<a href = "existing_users.php">User list</a><br><br>';
            }
            //because its april fools!
            if (date('jn') == '14') {
                echo '<div id="logoutjk"><a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" target="_blank" onclick="showLogout()">Logout</a></div><br>';
                echo '<div id="realLogout" style="display: none"><a href="logout.php"><u><i>Real</i></u> logout</a></div>';
            } else {
                echo '<a href="logout.php">Logout</a><br>';
            }
            ?>
            <!-- <a href="logout.php">Logout</a><br> -->

        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>

        <script>
            function showLogout() {
                var x = document.getElementById("realLogout");
                var y = document.getElementById("logoutjk");

                x.style.display = "block";
                y.style.display = "none";
            }
        </script>

    </body>
</html>