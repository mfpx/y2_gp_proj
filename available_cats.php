<?php
/*
 * CS22220 Group 3
 * Web booking system
 * available_cats.php developed by David C. and Nathan H.
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

//gets data for the category selection menu
$sql_cat = "SELECT cat_name FROM room_cats";
$result_cat = mysqli_query($conn, $sql_cat);

function catdelete($cat, $conn) {
    $del_catname = filter_var($cat, FILTER_SANITIZE_STRING);

    $sql = "DELETE FROM room_cats WHERE cat_name = '$del_catname'";
    $result = mysqli_query($conn, $sql);
    if ($result == true) {
        return 1;
    } else {
        return 0;
    }
}

function room_check($cat, $conn) {
    $del_catname = filter_var($cat, FILTER_SANITIZE_STRING);

    $sql = "SELECT room_name FROM rooms WHERE room_cat = '$del_catname'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $rooms = array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($rooms, $row['room_name']);
        }
        return $rooms;
    } else {
        return false;
    }
}

if ($_POST['cat_name'] && $admin == true) {
    $rmexist = room_check($_POST['cat_name'], $conn);
    if ($rmexist == false) {
        $delres = catdelete($_POST['cat_name'], $conn);
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
        <title>Available categories</title>
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

            <h2>Categories Available</h2>

            <div class="enclosure">

                <table>
                    <tr>
                        <th>Category name</th>
                        <?php
                        if ($admin == true) {
                            echo '<th>Delete?</th>';
                            echo '<th>Edit</th>';
                        }
                        ?>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_cat)) {
                        //generates a selection menu
                        echo '<tr>';
                        echo '<td>' . $row['cat_name'] . '</td>';
                        if ($admin == true) {
                            echo '<td><form method="post" action="available_cats.php">'
                            . '<input type="hidden" name="cat_name" value="' . $row['cat_name'] . '">'
                            . '<input type = "submit" value = "Delete">'
                            . '</form></td>';
                            echo '<td><form method="post" action="cat_edit.php">'
                            . '<input type="hidden" name="cat_name" value="'. $row['cat_name'] . '">'
                            . '<input type = "submit" value = "Edit">'
                            . '</form></td>';
                        }
                        echo '</tr>';
                        //if no rows returned
                        if (mysqli_num_rows($result_cat) == 0) {
                            echo 'You have no configured categories!';
                        }
                    }

                    if ($delres == true) {
                        echo "<div class = 'success'>Category deleted successfully!</div>";
                    } elseif (isset($delres)) {
                        echo "<div class = 'error'>An error occurred!</div>";
                    } elseif ($rmexist == true) {
                        for ($i = 0; $i <= sizeof($rmexist) - 1; $i++) {
                            $array_string .= $rmexist[$i];
                            if ($i < sizeof($rmexist) - 1) {
                                $array_string .= ', ';
                            } else {
                                $array_string .= ' ';
                            }
                        }
                        echo "<div class = 'error'>Room(s) " . $array_string . "are using the category!</div>";
                    }
                    ?>
                </table>

            </div>

        </main>

        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>
        
    </body>
</html>