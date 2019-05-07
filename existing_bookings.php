<?php
/*
 * CS22220 Group 3
 * Web booking system
 * existing_bookings.php developed by David C., Nathan H., Ysabel W. and Gethin L.
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

//gets data for the category selection menu
$sql_cat = "SELECT cat_name FROM room_cats";
$result_cat = mysqli_query($conn, $sql_cat);

private_file_header($conn);
$email = $_SESSION['email'];
$admin = admin_file_header($conn, false);

//if "del_id" is POSTed, booking will be deleted
if ($_POST['del_id']) {
    $del_id = filter_input(INPUT_POST, 'del_id', FILTER_SANITIZE_NUMBER_INT);
    $sql = "DELETE FROM bookings WHERE id = '$del_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $state = true;
    } else {
        $state = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
        <script src='http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js'></script>
        <script src='http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.1/fullcalendar.min.js'></script>
        <script src='http://fullcalendar.io/js/fullcalendar-2.3.1/lang-all.js'></script>
        <link rel="stylesheet" href="assets/style.css">
        <title>Existing bookings</title>
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
            <h2>Existing Bookings</h2>
            <div class="enclosure">

                <p style="font-family: arial, sans-serif; font-size: 15px;
                   text-decoration: underline; margin-bottom: 0"> Filter:</p>

                <select id="cat" style="margin-bottom: 15px">
                    <option value="" disabled selected hidden>Please choose an option to filter</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_cat)) {
                        //generates a selection menu
                        echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                    }
                    ?>
                </select>

                <?php
                if (!empty($state)) {
                    if ($state == true) {
                        echo '<div class="success">Booking deleted successfully!</div>';
                    } elseif ($state == false) {
                        echo '<div class="error">An error occured, please try again later!</div>';
                    }
                }
                if ($admin == false) {
                    $sql = "SELECT * FROM bookings WHERE email = '$email'";
                } elseif ($admin == true) {
                    $sql = "SELECT * FROM bookings";
                }
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    echo '<table>'
                    . '<tr>'
                    . '<th>ID</th>'
                    . '<th>Room</th>'
                    . '<th>Category</th>'
                    . '<th>Date</th>'
                    . '<th>Start Time</th>'
                    . '<th>End Time</th>'
                    . '<th>E-mail</th>'
                    . '<th>Reason</th>'
                    . '<th>Delete?</th>'
                    . '</tr>';
                    // output data of each row
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>'
                        . '<td>' . $row["id"] . '</td>'
                        . '<td>' . $row["room"] . '</td>'
                        . '<td>' . $row["cat"] . '</td>'
                        . '<td>' . $row["date"] . '</td>'
                        . '<td>' . $row["time_start"] . '</td>'
                        . '<td>' . $row["time_end"] . '</td>'
                        . '<td>' . $row["email"] . '</td>'
                        . '<td>' . $row["reason"] . '</td>';
                        echo '<td><form method="post" action="existing_bookings.php">'
                        . '<input type="hidden" name="del_id" value="' . $row['id'] . '">'
                        . '<input type = "submit" value = "Delete">'
                        . '</form></td>'
                        . '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '0 results';
                }
                ?>
            </div>


        </div>
    </main>
    <script>
        $("#cat").on("change",
                function () {
                    var selectedOption = $(this).find("option:selected").html();

                    $("table tr td:nth-child(3)").each(
                            function () {
                                if ($(this).html() !== selectedOption) {
                                    $(this).parent().hide();
                                } else {
                                    $(this).parent().show();
                                }
                            });
                });
    </script>
    <footer>
        <p><?php echo $footer_content; ?></p>
    </footer>
</body>
</html>
