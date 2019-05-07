<?php
/*
 * CS22220 Group 3
 * Web booking system
 * index.php developed by David C., Nathan H., Ysabel W. and Gethin L.
 */

require 'includes/cfg.php'; //website config
require 'includes/lib.php'; //library

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
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
            <a id="account" href="login.php">Login</a>
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
