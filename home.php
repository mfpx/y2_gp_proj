<?php
/*
 * CS22220 Group 3
 * Web booking system
 * index.php developed by David C., Nathan H., Ysabel W. and Gethin L.
 */

session_start();

require 'includes/cfg.php'; //website config
require 'includes/lib.php'; //library
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//important for login
private_file_header($conn);
$admin = admin_file_header($conn, false);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="assets/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>

        <title>Home</title>
    </head>
    <body>
        <script>

            $(document).ready(function () {
                var calendar = $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    events: 'load.php'
                });
            });

        </script>

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

            <h2>Existing Bookings</h2>

            <div class="enclosure">
                <div class="container">
                    <div id="calendar"></div>
                </div>
            </div>

        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>

    </body>
</html>
