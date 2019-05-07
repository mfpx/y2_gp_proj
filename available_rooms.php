<?php
/*
 * CS22220 Group 3
 * Web booking system
 * available_rooms.php developed by David C. and Nathan H.
 */

session_start();

require 'includes/cfg.php'; //database config
require 'includes/lib.php'; //library
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//gets the user data
$email = $_SESSION['email'];
$admin = admin_file_header($conn, false);
private_file_header($conn);

//get room information
$sql_rm = "SELECT * FROM rooms";
$result_rm = mysqli_query($conn, $sql_rm);

//gets data for the category selection menu
$sql_cat = "SELECT cat_name FROM room_cats";
$result_cat = mysqli_query($conn, $sql_cat);

function rmdelete($room, $conn) {
    $array = explode('--', $room);
    $del_rmname = filter_var($array[0], FILTER_SANITIZE_STRING);
    $del_rmcat = filter_var($array[1], FILTER_SANITIZE_STRING);

    $sql = "DELETE FROM rooms WHERE room_name = '$del_rmname' AND room_cat = '$del_rmcat'";
    $result = mysqli_query($conn, $sql);
    if ($result == true) {
        return 1;
    } else {
        return 0;
    }
}

function booking_check($room, $conn) {
    $array = explode('--', $room);
    $del_rmname = filter_var($array[0], FILTER_SANITIZE_STRING);
    $del_rmcat = filter_var($array[1], FILTER_SANITIZE_STRING);

    $sql = "SELECT id FROM bookings WHERE room = '$del_rmname' AND cat = '$del_rmcat'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $ids = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($ids, $row['id']);
        }
        return $ids;
    } else {
        return false;
    }
}

if ($_POST['room'] && $admin == true) {
    $bkexist = booking_check($_POST['room'], $conn);
    if ($bkexist == false) {
        $delres = rmdelete($_POST['room'], $conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">
        <script type="text/javascript" src="assets/jquery-3.3.1.js"></script>
        <title>Available rooms</title>
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

            <h2>Rooms Available</h2>

            <div class="enclosure">

                <p style="font-family: arial, sans-serif; font-size: 15px;
                   text-decoration: underline; margin-bottom: 0"> Filter:</p>

                <select id="cats" style="margin-bottom: 15px">
                    <option value="" disabled selected hidden>Please choose an option to filter</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_cat)) {
                        //generates a selection menu
                        echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                    }
                    ?>
                </select>

                <table>
                    <tr>
                        <th>Room Name</th>
                        <th>Room Location</th>
                        <?php
                        if ($admin == true) {
                            echo '<th>Delete?</th>';
                            echo '<th>Edit</th>';
                        }
                        ?>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_rm)) {
                        //generates a selection menu
                        echo '<tr>';
                        echo '<td>' . $row['room_name'] . '</td>';
                        echo '<td>' . $row['room_cat'] . '</td>';
                        if ($admin == true) {
                            echo '<td><form method="post" action="available_rooms.php">'
                            . '<input type="hidden" name="room" value="' . $row['room_name'] . '--' . $row['room_cat'] . '">'
                            . '<input type = "submit" value = "Delete">'
                            . '</form></td>';
                            echo '<td><form method="post" action="room_edit.php">'
                            . '<input type="hidden" name="rm_name" value="' . $row['room_name'] . '">'
                            . '<input type="hidden" name="rm_cat" value="' . $row['room_cat'] . '">'
                            . '<input type="submit" value="Edit">'
                            . '</form></td>';
                        }
                        echo '</tr>';
                        if (mysqli_num_rows($result_rm) == 0) {
                            echo 'You have no configured rooms!';
                        }
                    }

                    if ($delres == true) {
                        echo "<div class = 'success'>Room deleted successfully!</div>";
                    } elseif (isset($delres)) {
                        echo "<div class = 'error'>An error occurred!</div>";
                    } elseif ($bkexist == true) {
                        for ($i = 0; $i <= sizeof($bkexist) - 1; $i++) {
                            $array_string .= $bkexist[$i];
                            if ($i < sizeof($bkexist) - 1) {
                                $array_string .= ', ';
                            } else {
                                $array_string .= ' ';
                            }
                        }
                        echo "<div class = 'error'>Booking(s) " . $array_string . "are using the room!</div>";
                    }
                    ?>
                </table>

            </div>

        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>

        <script>
            $("#cats").on("change",
                    function () {
                        var selectedOption = $(this).find("option:selected").html();

                        $("table tr td:nth-child(2)").each(
                                function () {
                                    if ($(this).html() !== selectedOption) {
                                        $(this).parent().hide();
                                    } else {
                                        $(this).parent().show();
                                    }
                                });
                    });
        </script>

    </body>
</html>