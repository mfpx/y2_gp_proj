<?php

//some ips may be proxied or spoofed
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//generates a pseudo-random string of specific length
function token($length) {
    return bin2hex(random_bytes($length));
}

//destroys the session and invalidates the session cookie
function logout($url, $statusCode) {
    session_destroy();
    if (isset($_COOKIE['PHPSESSID'])) {
        setcookie('PHPSESSID', null, -1, '/');
    }
    redirect($url, $statusCode);
}

//generic redirect
function redirect($url, $statusCode) {
    header('Location: ' . $url, true, $statusCode);
    die();
}

//this invalidates any existing password reset requests for that user
function invalidate($email, $conn) {
    $sql = "SELECT valid FROM pass_resets WHERE email = '$email'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $valid = $row['valid'];
            if ($valid == 1) {
                $inv_sql = "UPDATE pass_resets SET valid = FALSE WHERE email = '$email'";
                mysqli_query($conn, $inv_sql);
            }
        }
    }
}

//iterator for comparing variables with numeric array items
function array_elem_check($array, $variable) {
    for ($x = 0;; $x++) {
        if ($array[$x] == $variable) {
            return true;
            //return $x //returns array element number
        }
    }
    return false;
}

//compares to arrays by key/value ONLY, NOT by identity
function array_compare($array1, $array2) {
    if ($array1 == $array2) {
        return true;
    } else {
        return false;
    }
}

function private_file_header($conn) {
    //gets the user data
    $email = $_SESSION['email'];
    $email_sql = "SELECT email FROM users WHERE email = '$email'";
    $email_result = mysqli_query($conn, $email_sql);
    $row = mysqli_fetch_assoc($email_result);

    /*
     * if the session user doesnt match db user, log the user out
     * this might be useful if the user was deleted while logged in
     */
    if ($email !== $row['email']) {
        logout('login.php', '302');
    }

    //this SHOULD prevent users from doing anything unless logged in
    if (!isset($_SESSION['email'])) {
        redirect('login.php', '302');
    }
}

function admin_file_header($conn, $redir) {
    $email = $_SESSION['email'];
    $sql = "SELECT admin FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $admin = $row['admin'];

    if ($admin != true && $redir === true) {
        redirect('available_rooms.php', '302');
    } else {
        return $admin;
    }
}

function email($to, $token) {
    include 'cfg.php';

    $url = $website_address . "pass_reset.php?token=$token";
    $subject = "Password reset for $website_name";
    $message = '
<html>
<head>
<title>Password reset</title>
</head>
<body>
Hi, you or somebody else (but hopefully you) requested for your password to be reset.<br>
If you requested this, please follow <a href="' . $url . '">THIS</a> link.<br>
However, if the link doesn\'t work, just copy this url into your browser\'s address bar:<br><br>
' . $url . '
</body>
</html>
';

// Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
    $headers .= 'From: <' . $noreply . '>' . "\r\n";

    mail($to, $subject, $message, $headers);
}
