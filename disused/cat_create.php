<!DOCTYPE html>
<html>
    <head>
        <title>Category creation</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        Category name: <br>
        <form method="post" action="cat_create.php">
            <input type="text" name="catname" required>
            <input type="submit" value="Create">
        </form>
    </body>
</html>

<?php

/*
 * CS22220 Group 3
 * Web booking system
 * cat_create.php developed by David C.
 */

require 'includes/cfg.php'; //database config
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$catname = filter_input(INPUT_POST, 'catname', FILTER_SANITIZE_STRING);

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

if ($_POST['catname']) {
    $exists = cat_check($catname, $conn);
    if ($exists == true) {
        echo 'Category "' . $catname . '" exists!';
    } elseif ($exists == false) {
        $cat_insert_query = "INSERT INTO room_cats VALUES('$catname')";
        if (mysqli_query($conn, $cat_insert_query)) {
            echo 'Category "' . $catname . '" created successfully!';
        } else {
            echo 'Something went wrong!';
        }
    } elseif ($exists == -1) {
        echo 'Something went wrong!';
    }
}
