<?php

/*
 * CS22220 Group 3
 * Web booking system
 * index.php developed by David C.
 */

require 'includes/cfg.php'; //database config
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//gets data for the room selection menu
$sql = "SELECT * FROM rooms";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="style.css">
        <script src="jquery-3.3.1.min.js"></script>
        <title>Index CS22220 Booking System</title>

    </head>

    <body>

        <!-- Page title -->
        <h1><u>Room Bookings</u></h1>

        <!-- Form to gain data from user -->
        <div class="form">
            <form id="bookingForm" action="form.php" method="post">
                <!-- Getting users First name -->
                <p>First Name:</p>
                <input type="text" required="required"
                       name="firstName" placeholder="First Name"
                       oninvalid="this.setCustomValidity('Please enter your first name')"
                       oninput="this.setCustomValidity('')" disabled>
                <!-- Getting users Last name -->
                <p>Last Name:</p>
                <input type="text" required="required"
                       name="lastName" placeholder="Last Name"
                       oninvalid="this.setCustomValidity('Please enter your last name')"
                       oninput="this.setCustomValidity('')" disabled>
                <!-- Getting users Email -->
                <p>Email:</p>
                <input type="email" required="required"
                       name="email" placeholder="abc@aber.ac.uk"
                       oninvalid="this.setCustomValidity('Please enter a valid email')"
                       oninput="this.setCustomValidity('')" disabled>
                <!-- Getting the date the user wants the room for -->
                <p>Date:</p>
                <input type="date" required="required"
                       name="date"
                       oninvalid="this.setCustomValidity('Please select a date')"
                       oninput="this.setCustomValidity('')">
                <!-- Start time -->
                <p>Start time:</p>
                <input type="time" required
                       name="timestart"
                       oninvalid="this.setCustomValidity('Please select the starting time')"
                       oninput="this.setCustomValidity('')">
                <!-- End time -->
                <p>End time:</p>
                <input type="time" required
                       name="timend"
                       oninvalid="this.setCustomValidity('Please select the ending time')"
                       oninput="this.setCustomValidity('')">
                <!-- Getting required room -->
                <p>Room:</p>
                <?php
                //the dropdown could do with some css styling
                if (mysqli_num_rows($result) > 0) {
                    echo '<select name="rmname">';
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['room_name'] . '">' . $row['room_name'] . '</option>';
                    }
                    echo '</select><br>';
                } else {
                    //tells the user that they dont have any conf'd rooms
                    echo '<br>There are no configured rooms!';
                }
                ?>
                <!-- Room category auto update -->
                <p>Room category:</p>
                <input type="text" required
                       name="rmcat" disabled>
                <!-- Reason -->
                <p>Reason:</p>
                <input type="text" required="required"
                       name="reason" placeholder="Booking reason"
                       maxlength="100">
                <br><br>
                <input type="submit" name="submit" value="Confirm Booking">
            </form>
        </div>
    </body>
</html>

