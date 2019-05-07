<?php

/*
 * CS22220 Group 3
 * Web booking system
 * loginHandler.php developed by David C.
 */

/*
 * Auth types:
 * basic - returns whether the user is valid or not
 * admin - returns admin status
 * full - returns all user data
 * 
 * NOTE: Data will only be returned if user credentials are valid
 * Returns -1 if credentials are invalid
 */

function login($email, $plain_pass, $authtype) {
    require 'cfg.php';
    $conn = mysqli_connect($host, $username, $password, $database);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (valid($email, $plain_pass, $conn)) {
        switch ($authtype) {
            case "basic":
                return true;
            case "admin":
                $userdata = userdata($email, $conn);
                return $userdata['admin'];
            case "full":
                return userdata($email, $conn);
        }
    } else {
        return -1;
    }
}

function valid($email, $pass, $conn) {
    $sql = "SELECT email, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $data = mysqli_fetch_assoc($result);
        $db_email = $data['email'];
        $db_passhash = $data['password'];
    }

    if (hash_equals($db_email, $email) && password_verify($pass, $db_passhash)) {
        return true;
    } else {
        return false;
    }
}

function userdata($email, $conn) {
    $sql = "SELECT first_name, surname, admin FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);

    return $data;
}
