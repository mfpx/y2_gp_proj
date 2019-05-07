<?php

/*
 * CS22220 Group 3
 * Web booking system
 * room_create.php developed by David C.
 */

require 'includes/cfg.php'; //database config
// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
    if($dbval_rm == $rmname){
        return true;
    } elseif($dbval_rm != $rmname) {
        return false;
    } else {
        return -1; //this shouldnt happen
    }
}

//inserts data into db
function insert($rmname,$cat,$conn){
    $insert_sql="INSERT INTO rooms VALUES('$rmname','$cat')";
    if(mysqli_query($conn,$insert_sql)){
        return true;
    } else {
        return mysqli_error($conn);
    }
}

//simple way to check for submitted data
//this should probably be improved in the future
if($_POST){
    $rmname = filter_input(INPUT_POST, 'rmname', FILTER_SANITIZE_STRING);
    $cat = filter_input(INPUT_POST, 'cat', FILTER_SANITIZE_STRING);
    
    $room_exists = room_check($rmname, $cat, $conn);
    if($room_exists == true){
        echo 'Room "' . $rmname . '" exists!';
    } elseif($room_exists == false) {
        //actual data insertion
        $insert_msg = insert($rmname,$cat,$conn);
        if($insert_msg == true){
            echo 'Room created successfully!';
        } else {
            echo $insert_msg; //sql or db error
        }
    } elseif($room_exists == -1) {
        'Something went wrong!'; //the code shouldnt get to here
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Room creation</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <form method="post" action="room_create.php">
            Room name: <br>
            <input type="text" name="rmname" required><br>
            Room category: <br>
            <?php
            if (mysqli_num_rows($result) > 0) {
                //echo "Room category: <br>";
                echo '<select name="cat">';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="'.$row['cat_name'].'">'.$row['cat_name'].'</option>';
                }
                echo "</select><br>";
                echo '<input type="submit" value="Create">';
            } else {
                //tells the user that they dont have anything in room_cats
                echo '<br>There are no configured categories!';
            }
            ?>
        </form>
    </body>
</html>
