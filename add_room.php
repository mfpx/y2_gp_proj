<?php
/*
 * CS22220 Group 3
 * Web booking system
 * add_room.php developed by David C. and Nathan H.
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

private_file_header($conn);
$email = $_SESSION['email'];
$admin = admin_file_header($conn, true);


//gets data for the category selection menu
$sql = "SELECT * FROM room_cats";
$result = mysqli_query($conn, $sql);

//checks if the room exists - it doesnt care about the category
function room_check($rmname, $rmcat, $conn) {
    //check if room exists
    $sql = "SELECT room_name, room_cat FROM rooms WHERE room_name = '$rmname' AND room_cat = '$rmcat'";
    $result = mysqli_query($conn, $sql);
    $value = mysqli_fetch_object($result);

    //database values
    $dbval_rm = $value->room_name;

    //multiple returns
    if ($dbval_rm == $rmname) {
        return true;
    } elseif ($dbval_rm != $rmname) {
        return false;
    } else {
        return -1; //this shouldnt happen
    }
}

//inserts data into db
function insert($rmname, $cat, $conn) {
    $insert_sql = "INSERT INTO rooms VALUES('$rmname','$cat')";
    if (mysqli_query($conn, $insert_sql)) {
        return true;
    } else {
        return mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">

        <title>Room addition</title>
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
                    <a href="add_room.php">Add Room</a>
                    <a href="add_cat.php">Add Category</a>
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

            <h2>Add a Room</h2>

            <div class="form">
                <form id="addRoomForm" action="add_room.php" method="post">
                    <h3>Name:</h3>
                    <input type="text" required
                           name="name" placeholder="Please Enter the Room Name">
                    <h3>Location:</h3>

                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        //echo "Room category: <br>";
                        echo '<select name="category" required>';
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                        }
                        echo "</select>";
                    } else {
                        //tells the user that they dont have anything in room_cats
                        echo '<br>There are no configured categories!';
                    }
                    ?>

                    <br><br>
                    <input type="submit" name="submit" value="Confirm New Room">
                    <?php
                    //simple way to check for submitted data
                    //this should probably be improved in the future
                    if ($_POST['name'] && $_POST['category']) {
                        $rmname = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                        $cat = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

                        $room_exists = room_check($rmname, $cat, $conn);
                        if ($room_exists == true) {
                            echo '<div class ="error">Room "' . $rmname . '" exists!</div>';
                        } elseif ($room_exists == false) {
                            //actual data insertion
                            $insert_msg = insert($rmname, $cat, $conn);
                            if ($insert_msg == true) {
                                echo '<br><br><div class ="success">Room created successfully!</div>';
                            } else {
                                echo $insert_msg; //sql or db error
                            }
                        } elseif ($room_exists == -1) {
                            echo '<div class ="error">Something went wrong!</div>'; //the code shouldnt get to here
                        }
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