<?php
/*
 * CS22220 Group 3
 * Web booking system
 * room_edit.php developed by David C.
 */

require 'includes/cfg.php'; //database config
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
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

function full_edit_queries($rmname, $newname, $oldcat, $newcat, $conn){
    if(edit($newname, $rmname, $oldcat, '', $conn, 'name')){
        if(edit('', $newname, $oldcat, $newcat, $conn, 'cat')){
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
        echo 'Old and new categories are the same!';
    } elseif ($rmname == $newname && $oldcat != $newcat) {
        echo 'Old and new names are the same!';
    } elseif ($rmname == $newname && $oldcat == $newcat) {
        echo 'Details entered are the same as the current ones!';
    }

    if ($oldcat != $newcat && $rmname != $newname) {
        if (existing_check($newname, $newcat, $conn)) {
            echo 'Room with the entered details already exists!';
        } elseif (full_edit_queries($rmname, $newname, $oldcat, $newcat, $conn)) {
            echo 'Details successfully changed!';
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
    </head>
    <body>
        If you don't want to edit a category, please select the room's current category!<br><br>
        <form method="post" action="room_edit.php">
            Room to edit: <br>
            <?php
            //the dropdown could do with some css styling
            if (mysqli_num_rows($result_rm) > 0) {
                echo '<select name="room">';
                while ($row = mysqli_fetch_assoc($result_rm)) {
                    //generates a selection menu
                    echo '<option value="' . $row['room_name'] . '--' . $row['room_cat'] . '">' . $row['room_name'] . ' - ' . $row['room_cat'] . '</option>';
                }
                echo '</select><br><br>';
                echo 'New category:<br>';
                echo '<select name="newrmcat">';
                while ($row = mysqli_fetch_assoc($result_cat)) {
                    //generates a selection menu
                    echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                }
                echo '</select><br><br>';
                echo 'New room name:<br>';
                echo '<input type="text" name="newrmname"><br>';
                echo '<input type="submit" value="Edit"><br><br>';
            } else {
                //tells the user that they dont have any categories
                echo '<br>There are no configured rooms!';
            }

            if ($_POST['room']) {
                $room = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_STRING);
                $passed_room_array = explode("--", $room);
                $rmname = $passed_room_array[0];
                $oldcat = $passed_room_array[1];

                if ($_POST['newrmcat'] && !$_POST['newrmname']) {
                    $newcat = filter_input(INPUT_POST, 'newrmcat', FILTER_SANITIZE_STRING);
                    category($oldcat, $newcat, $rmname, $conn);
                } elseif ($_POST['newrmname'] && $_POST['newrmcat'] == $oldcat) {
                    $newname = filter_input(INPUT_POST, 'newrmname', FILTER_SANITIZE_STRING);
                    name($rmname, $newname, $oldcat, $conn);
                } elseif ($_POST['newrmname'] && $_POST['newrmcat']) {
                    $newcat = filter_input(INPUT_POST, 'newrmcat', FILTER_SANITIZE_STRING);
                    $newname = filter_input(INPUT_POST, 'newrmname', FILTER_SANITIZE_STRING);
                    full_edit($rmname, $newname, $oldcat, $newcat, $conn); 
                }
            }
            ?>
        </form>
    </body>
</html>