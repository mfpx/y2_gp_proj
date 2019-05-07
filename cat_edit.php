<?php
/*
 * CS22220 Group 3
 * Web booking system
 * edit.php developed by Nathan H. and David C.
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

//admin and generic login headers
private_file_header($conn);
$admin = admin_file_header($conn, true);

if ($_POST['cat_name']) {
    $name = filter_input(INPUT_POST, 'cat_name', FILTER_SANITIZE_STRING);
} else {
    redirect('existing_bookings.php', '302');
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/style.css">
        <title>Category edit</title>
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
            <h2>Category edit</h2>

            <form method="post" action="edit.php">
                Editing category: <?php echo $name; ?> <br>
                New category name:<br>
                <input type="text" name="newcatname" required><br>
                <input type="hidden" name="cat_name" value="<?php echo $name; ?>">
                <input type="hidden" name="sent" value="true">
                <input type="submit" value="Edit"><br>

                <?php
                if ($_POST['sent'] == true && empty($_POST['newcatname'])) {
                    echo '<br>You did not enter the new name!';
                } elseif ($_POST['newcatname']) {
                    $newcatname = filter_input(INPUT_POST, 'newcatname', FILTER_SANITIZE_STRING);
                    $exists = cat_check($newcatname, $conn);
                    if ($exists) {
                        echo 'The category ' . $newcatname . ' exists!';
                    } elseif (!$exists) {
                        $renamesql = "UPDATE room_cats SET cat_name = '$newcatname' WHERE cat_name = '$name';";
                        if (mysqli_query($conn, $renamesql)) {
                            redirect('available_cats.php', '302');
                        } else {
                            echo 'Something went wrong!';
                        }
                    }
                }

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
            </form>
        </main>
        <footer>
            <p><?php echo $footer_content; ?></p>
        </footer>
    </body>
</html>