<?php
/*
 * CS22220 Group 3
 * Web booking system
 * room_edit.php developed by David C.
 */

session_start();

require 'includes/cfg.php'; //database config
require 'includes/lib.php'; //library
//
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//admin and generic login headers
private_file_header($conn);
$admin = admin_file_header($conn, true);

//should prevent direct access without any data POSTed
if (!$_POST['rm_name'] && !$_POST['rm_cat']) {
    redirect('available_rooms.php', '302');
}

//get room information
$sql_rm = "SELECT room_name, room_cat FROM rooms";
$result_rm = mysqli_query($conn, $sql_rm);

//gets data for the category selection menu
$sql_cat = "SELECT cat_name FROM room_cats";
$result_cat = mysqli_query($conn, $sql_cat);

function existing_check($rmname, $rmcat, $conn) {
    $sql = "SELECT room_name, room_cat FROM rooms WHERE room_name = '$rmname' AND room_cat = '$rmcat'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

function name($rmname, $newname, $oldcat, $conn) {
    if ($rmname == $newname) {
        echo 'Old and new names are the same!';
    } elseif (existing_check($newname, $oldcat, $conn)) {
        echo 'Room with your selected name already exists!';
    } else {
        $edited = edit($newname, $rmname, $oldcat, '', $conn, 'name');
        if ($edited) {
            echo 'Name successfully changed!';
        } elseif (!$edited) {
            echo 'Something went wrong!';
        }
    }
}

function category($oldcat, $newcat, $rmname, $conn) {
    if ($oldcat == $newcat) {
        echo 'Old and new categories are the same!';
    } elseif (existing_check($rmname, $newcat, $conn)) {
        echo 'Room with your selected category already exists!';
    } else {
        $edited = edit('', $rmname, $oldcat, $newcat, $conn, 'cat');
        if ($edited) {
            echo 'Category successfully changed!';
        } elseif (!$edited) {
            echo 'Something went wrong!';
        }
    }
}

function edit($newname, $rmname, $oldcat, $newcat, $conn, $function) {
    if ($function == 'cat') {
        $sql = "UPDATE rooms SET room_cat = '$newcat' WHERE room_name = '$rmname' AND room_cat = '$oldcat'";
        if (mysqli_query($conn, $sql)) {
            return true;
        } else {
            return false;
        }
    } elseif ($function == 'name') {
        $sql = "UPDATE rooms SET room_name = '$newname' WHERE room_name = '$rmname' AND room_cat = '$oldcat'";
        if (mysqli_query($conn, $sql)) {
            return true;
        } else {
            return false;
        }
    }
}

function full_edit_queries($rmname, $newname, $oldcat, $newcat, $conn) {
    if (edit($newname, $rmname, $oldcat, '', $conn, 'name')) {
        if (edit('', $newname, $oldcat, $newcat, $conn, 'cat')) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function full_edit($rmname, $newname, $oldcat, $newcat, $conn) {
    if ($oldcat == $newcat && $rmname != $newname) {
        category($oldcat, $newcat, $rmname, $conn);
    } elseif ($rmname == $newname && $oldcat != $newcat) {
        name($rmname, $newname, $oldcat, $conn);
    } elseif ($rmname == $newname && $oldcat == $newcat) {
        echo 'Details entered are the same as the current ones!';
    }

    if ($oldcat != $newcat && $rmname != $newname) {
        if (existing_check($newname, $newcat, $conn)) {
            echo 'Room with the entered details already exists!';
        } elseif (full_edit_queries($rmname, $newname, $oldcat, $newcat, $conn)) {
            redirect('available_rooms.php?state=1', '302');
        } else {
            echo 'Something went wrong!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Room edit</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">
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
            <h2>Room edit</h2>

            If you don't want to edit category or name, please select/enter the current one!<br><br>
            <form method="post" action="room_edit.php">
                Room to edit: 
                <?php
                $room = filter_input(INPUT_POST, 'rm_name', FILTER_SANITIZE_STRING);
                $category = filter_input(INPUT_POST, 'rm_cat', FILTER_SANITIZE_STRING);
                echo $room . ' - ' . $category . '<br>';

                echo 'New category:<br>'
                . '<select name="newrmcat" required>';
                if (mysqli_num_rows($result_rm) > 0) {
                    while ($row = mysqli_fetch_assoc($result_cat)) {
                        //generates a selection menu
                        echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                    }
                } else {
                    echo 'There are no configured categories!';
                }
                echo '</select><br><br>'
                . 'New room name:<br>'
                . '<input type="text" name="newrmname" required><br>'
                . '<input type="hidden" name="room" value="' . $room . '--' . $category . '">'
                . '<input type="hidden" name="rm_name" value="' . $room . '">'
                . '<input type="hidden" name="rm_cat" value="' . $category . '">'
                . '<input type="submit" value="Edit"><br><br>';

                if ($_POST['room']) {
                    $room = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_STRING);
                    $passed_room_array = explode("--", $room);
                    $rmname = $passed_room_array[0];
                    $oldcat = $passed_room_array[1];

                    if ($_POST['newrmname'] && $_POST['newrmcat']) {
                        $newcat = filter_input(INPUT_POST, 'newrmcat', FILTER_SANITIZE_STRING);
                        $newname = filter_input(INPUT_POST, 'newrmname', FILTER_SANITIZE_STRING);
                        full_edit($rmname, $newname, $oldcat, $newcat, $conn);
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