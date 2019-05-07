<?php

/*
 * CS22220 Group 3
 * Web booking system
 * cron.php developed by David C.
 */

require 'cfg.php'; //database config
//
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "DELETE FROM bookings WHERE bookings.date < CURRENT_DATE";
mysqli_query($conn, $sql); //the output doesnt matter - might later implement error logs