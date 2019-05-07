<?php

/*
 * CS22220 Group 3
 * Web booking system
 * cat_edit.php developed by David C.
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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Category edit</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <form method="post" action="cat_edit.php">
            Category to edit: <br>
            <?php
            //the dropdown could do with some css styling
            if (mysqli_num_rows($result) > 0) {
                echo '<select name="catname">';
                while ($row = mysqli_fetch_assoc($result)) {
                    //generates a selection menu
                    echo '<option value="' . $row['cat_name'] . '">' . $row['cat_name'] . '</option>';
                }
                echo '</select><br><br>';
                echo 'New category name:<br>';
                echo '<input type="text" name="newcatname"><br>';
                echo '<input type="submit" value="Edit"><br><br>';
            } else {
                //tells the user that they dont have any categories
                echo '<br>There are no configured categories!';
            }
            if ($_POST['catname'] && !$_POST['newcatname']) {
                echo '<br>You did not enter the new name!';
            } elseif ($_POST['catname'] && $_POST['newcatname']) {
                $oldcatname = filter_input(INPUT_POST, 'catname', FILTER_SANITIZE_STRING);
                $newcatname = filter_input(INPUT_POST, 'newcatname', FILTER_SANITIZE_STRING);
                $exists = cat_check($newcatname, $conn);
                if ($exists) {
                    echo 'The category ' . $newcatname . ' exists!';
                } elseif (!$exists) {
                    $renamesql = "UPDATE room_cats SET cat_name = '$newcatname' WHERE cat_name = '$oldcatname';";
                    if (mysqli_query($conn, $renamesql)) {
                        echo 'Category "' . $oldcatname . '" renamed to "' . $newcatname . '" successfully!';
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
    </body>
</html>