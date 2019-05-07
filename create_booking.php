<?php
/*
 * CS22220 Group 3
 * Web booking system
 * create_booking.php developed by Nathan H. and David C.
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

//this SHOULD prevent the user from doing anything unless logged in
if (!isset($_SESSION['email'])) {
    redirect('login.php', '302');
}

//get room information
$sql_rm = "SELECT * FROM rooms";
$result_rm = mysqli_query($conn, $sql_rm);

//gets the user data
$email = $_SESSION['email'];
$email_sql = "SELECT * FROM users WHERE email = '$email'";
$email_result = mysqli_query($conn, $email_sql);
$row = mysqli_fetch_assoc($email_result);

/*
 * if the session user doesnt match db user, log the user out
 * this might be useful if the user was deleted while logged in
 */
if ($email !== $row['email']) {
    logout('login.php', '302');
}

//user variables
$fname = $row['first_name'];
$lname = $row['surname'];
$admin = $row['admin'];

//checks if the entered data is valid
function validity_check($date, $currentDate, $startTime, $currentTime) {
    //the below can be a single statement, since both conditions cant be true at once
    if ($date < $currentDate) {
        //echo "selected date " . $date . " is before today!";
        return -1;
    } elseif ($startTime < $currentTime && $currentDate == $date) {
        //echo "start time is before current time, on current date";
        return -2;
    } else {
        return true;
    }
}

//checks if a booking already exists
function existing_check($conn, $room, $cat, $date, $time) {
    $sql = "SELECT room, cat, date, time_start FROM bookings WHERE room = '$room' AND cat = '$cat'"
            . "AND date = '$date' AND time_start = '$time'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

//if data was POSTed we will do the main actions
if ($_POST['room'] && $_POST['reason']) {
    $room = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_STRING);
    $passed_room_array = explode("--", $room);

    //variables to send to the database
    $rmname = $passed_room_array[0];
    $rmcat = $passed_room_array[1];
    $bk_date = preg_replace("([^0-9-])", "", $_POST['date']);
    $startTime = preg_replace("([^0-9:])", "", $_POST['startTime']);
    $duration = preg_replace("([^0-9:])", "", $_POST['duration']); //might need this format later
    $duration_secs = $duration * 3600;
    $endTime = date('H:i:s', strtotime("+$duration_secs seconds", strtotime($startTime)));
    $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
    $currentDate = date("Y-m-d"); //non user var
    $currentTime = date("H:i:s"); //non user var

    $exists = existing_check($conn, $rmname, $rmcat, $bk_date, $startTime);
    if ($exists === false && $_POST['repeatCheck'] == false) {
        $valid = validity_check($bk_date, $currentDate, $startTime, $currentTime);
        if ($valid === true) {
            $bk_sql = "INSERT INTO bookings VALUES('','','$rmname','$rmcat','$currentDate','$currentTime','$bk_date',"
                    . "'$startTime','$endTime','$email','$reason')";
            $bk_result = mysqli_query($conn, $bk_sql);
        }
    }
}

//unused
if ($_POST['repeatCheck'] && !$_POST['frequency'] && !$_POST['length']) {
    $repeatState = 0;
} elseif ($_POST['repeatCheck'] && $_POST['frequency'] && !$_POST['length']) {
    $repeatState = -1;
} elseif ($_POST['repeatCheck'] && !$_POST['frequency'] && $_POST['length']) {
    $repeatState = -2;
} elseif ($_POST['repeatCheck'] && $_POST['frequency'] && $_POST['length']) {
    $repeatState = 1;

    $rid_query = "SELECT id, repeatid FROM bookings ORDER BY repeatid DESC LIMIT 1";
    $res = mysqli_query($conn, $rid_query);
    $ids = mysqli_fetch_assoc($res);
    $new_rid = $ids['repeatid'] + 1;

    $frequency = filter_input(INPUT_POST, 'frequency', FILTER_SANITIZE_NUMBER_INT);
    $length = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_INT);
    $funit = $_POST['funit'];
    $lunit = $_POST['lunit'];

    if ($admin == true && $syserrors === true) {
        echo '<br>freq: ' . $frequency . '<br>';

        echo 'length: ' . $length . '<br>';

        echo 'new rid: ' . $new_rid . '<br>';
        var_dump($ids);
        echo '<br>';

        echo 'funit: ' . $funit . '<br>';
        echo 'lunit: ' . $lunit . '<br>';
    }


    if ($funit != $lunit) {
        if (($funit != 'months' || $lunit != 'weeks') || ($funit != 'months' && $lunit != 'weeks')) {
            echo 'repeat every ' . $frequency . ' ' . $funit . ' for ' . $length . ' ' . $lunit . '<br>';

            //hacky stuff
            if ($lunit == 'months' && $funit == 'weeks') {
                $length = $length * 4;
                $length = $length / $frequency;
            } elseif ($lunit == 'months' && $funit == 'days'){
                $length = $length * 4;
                $length = $length * 7;
                $length = $length / $frequency;
            } elseif ($lunit == 'weeks' && $funit == 'days'){
                $length = $length * $frequency;
            }

            echo $currentDate . '<br>';
            for ($x = 1; $x <= $length; $x++) {
                if ($x == 1) {
                    $date_increment = $bk_date;
                }
                $valid = validity_check($date_increment, $currentDate, $startTime, $currentTime);
                if ($exists == false && $_POST['repeatCheck'] == 'on') {
                    if ($valid == true) {
                        $bk_sql = "INSERT INTO bookings VALUES('','$new_rid','$rmname','$rmcat','$currentDate','$currentTime','$date_increment',"
                                . "'$startTime','$endTime','$email','$reason')";
                        echo $date_increment = date("Y-m-d", strtotime($bk_date . ' + ' . $frequency * $x . ' ' . $funit)) . '<br>';
                        $bk_result = mysqli_query($conn, $bk_sql);
                        echo mysqli_error($conn);
                    }
                }
            }
        }
    } else {
        $bk_valid = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <!-- calling the stylesheet for the index page -->
        <link rel="stylesheet" href="assets/style.css">
        <script>
            function repeat() {
                var checkBox = document.getElementById("repeatCheck").checked;
                var repeatForm = document.getElementById("repeatFields");

                if (checkBox == true) {
                    repeatForm.style.display = "block";
                } else {
                    repeatForm.style.display = "none";
                }
            }
        </script>

        <title>Room booking</title>
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

            <h2>Book a Room</h2>
            <!-- Form to gain data from user -->
            <div class="form">
                <form id="bookingForm" action="create_booking.php" method="post">
                    <h3>Booking as:</h3>
                    <?php
                    echo $fname . ' ' . $lname . ' ' . '(' . $_SESSION['email'] . ')';
                    ?>
                    <!-- Getting required room -->
                    <h3>Room:</h3>
                    <select class="selectMenu" name="room" required>
                        <?php
                        if (mysqli_num_rows($result_rm) > 0) {
                            while ($row = mysqli_fetch_assoc($result_rm)) {
                                //generates a selection menu
                                echo '<option value="' . $row['room_name'] . '--' . $row['room_cat'] . '">' . $row['room_name'] . ' - ' . $row['room_cat'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <!-- Getting the date the user wants the room for -->
                    <h3>Date:</h3>
                    <input type="date" required
                           name="date" placeholder="dd/mm/yyyy"
                           oninvalid="this.setCustomValidity('Please select a date')"
                           oninput="this.setCustomValidity('')">
                    <!-- Getting the start time the user whats to book a room from -->
                    <h3>Booking Start Time:</h3>
                    <input type="time" required
                           name="startTime" placeholder="00:00"
                           step="3600" min="08:00" max="22:00"
                           oninvalid="this.setCustomValidity('Please select a time')"
                           oninput="this.setCustomValidity('')">
                    <!-- Getting the end time the user whats to book a room from -->
                    <h3>Booking length:</h3>
                    <input type="time" required
                           name="duration" placeholder="00:00"
                           step="3600" min="01:00" max="14:00"
                           oninvalid="this.setCustomValidity('Please select a time')"
                           oninput="this.setCustomValidity('')">
                    <!-- Getting the reason for the booking -->
                    <h3>Reason For Booking:</h3>
                    <input type="textarea" name="reason"
                           placeholder="Please enter the reason you are booking this room"
                           maxlength="250">
                    <br><br>
                    <h3>Repeat:</h3>
                    <input type="checkbox" name="repeatCheck"
                           id="repeatCheck" onclick="repeat()"><br>
                    <div style="display:none" id="repeatFields">
                        Repeat every <input type="number" min="1" name="frequency">
                        <select name="funit">
                            <option value="days">days</option>
                            <option value="weeks">weeks</option>
                        </select><br>
                        For <input type="number" min="1" name="length">
                        <select name="lunit">
                            <option value="months">months</option>
                        </select>
                    </div>
                    <br><br>
                    <input type="submit" name="submit" value="Confirm Booking">

                    <?php
                    //booking creation errors
                    if ($valid === -1) {
                        echo '<div class="error">Booking date is before today!</div>';
                    } elseif ($valid === -2) {
                        echo '<div class="error">Booking time is before now, on current date!</div>';
                    } elseif ($exists === true) {
                        echo '<div class="error">Booking exists!</div>';
                    } elseif ($bk_result) {
                        echo '<div class="success">Booking created!</div>';
                    } elseif ($bk_valid === false) {
                        echo '<div class="error">Something went wrong, please try again!</div>';
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