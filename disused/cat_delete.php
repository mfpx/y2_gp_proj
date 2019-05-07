<?php

/*
 * CS22220 Group 3
 * Web booking system
 * cat_delete.php developed by David C.
 */

require 'includes/cfg.php'; //database config
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//gets data for the room selection menu
$sql = "SELECT cat_name FROM room_cats";
$result = mysqli_query($conn, $sql);

//checks if any rooms have the category
function room_clash($catname, $conn) {
    //check if cat exists
    $sql = "SELECT room_name, room_cat FROM rooms WHERE room_cat = '$catname'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return $result;
    } elseif (mysqli_num_rows($result) == 0) {
        return false;
    }
}

function delete($cat, $conn) {
    $sql = "DELETE FROM room_cats WHERE cat_name = '$cat'";
    
    if(mysqli_query($conn, $sql)){
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Category delete</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <form method="post" action="cat_delete.php">
            Category to delete: <br>
            <?php
            //the dropdown could do with some css styling
            if (mysqli_num_rows($result) > 0) {
                echo '<select name="catname">';
                while ($row = mysqli_fetch_assoc($result)) {
                    //generates a selection menu
                    echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                }
                echo '</select><br><br>';
                echo '<input type="submit" value="Delete"><br><br>';
            } else {
                //tells the user that they dont have any categories
                echo '<br>There are no configured categories!';
            }

            if ($_POST['catname']) {
                $catname = filter_input(INPUT_POST, 'catname', FILTER_SANITIZE_STRING);
                $rm_check = room_clash($catname, $conn);
                if ($rm_check != false) {
                    echo 'Cannot delete! The following rooms are using the category: <br>';
                    while ($row = mysqli_fetch_assoc($rm_check)) {
                        echo $row['room_name'] . '<br>';
                    }
                } elseif ($rm_check == false) {
                    if(delete($catname, $conn)){
                        echo 'Category deleted successfully!';
                    } else {
                        echo 'Something went wrong!';
                    }
                }
            }
            ?>
        </form>
    </body>
</html>