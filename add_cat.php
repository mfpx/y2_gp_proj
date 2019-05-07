<?php
/*
 * CS22220 Group 3
 * Web booking system
 * add_cat.php developed by David C. and Nathan H.
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

//checks if the category exists
function cat_check($catname, $conn) {
    //check if cat exists
    $sql = "SELECT cat_name FROM room_cats WHERE cat_name = '$catname'";
    $result = mysqli_query($conn, $sql);
    $value = mysqli_fetch_object($result);
    $dbval = $value->cat_name;

    //multiple returns
    if ($dbval == $catname) {
        return true;
    } elseif ($dbval != $catname) {
        return false;
    } else {
        return -1; //this shouldnt happen
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">

        <title>Category addition</title>
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

            <h2>Add a Category</h2>

            <div class="form">
                <form id="addRoomForm" action="add_cat.php" method="post">
                    <h3>Name:</h3>
                    <input type="text" required
                           name="catname" placeholder="Please Enter the Category Name">
                    <br><br>
                    <input type="submit" name="submit" value="Confirm New Category">
                    <?php
                    if ($_POST['catname']) {
                        $catname = filter_input(INPUT_POST, 'catname', FILTER_SANITIZE_STRING);
                        $exists = cat_check($catname, $conn);
                        if ($exists == true) {
                            echo '<div class ="error">Category "' . $catname . '" exists!</div>';
                        } elseif ($exists == false) {
                            $cat_insert_query = "INSERT INTO room_cats VALUES('$catname')";
                            if (mysqli_query($conn, $cat_insert_query)) {
                                echo '<div class="success">Category "' . $catname . '" created successfully!</div>';
                            } else {
                                echo '<div class ="error">Something went wrong!</div>';
                            }
                        } elseif ($exists == -1) {
                            echo '<div class ="error">Something went wrong!</div>';
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