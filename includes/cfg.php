<?php

/*
 * CS22220 Group 3
 * Web booking system
 * cfg.php developed by David C.
 */

//MySQL Server configuration
//MySQL Database address (host)
$host = 'DBHOST';
//Database username
$username = 'DBUSER';
//Database password
$password = 'DBPASS';
//Actual database to use
$database = 'DBNAME';

//WEBSITE CONFIGURATION BELOW
//This is the website's footer, you might want to edit this
//This will appear on the bottom of every page
$footer_content = 'Developed by David C, Matthew C, Nathan  H, Gethin L, Matthew M, Jack O, Jack V, and Ysabel W';

//Name of the website - this is the underlined header at the top
$website_name = 'Web Booking System';

/*
 * Website address (FQDN) and directory if applicable - WITH trailing slash
 * e.g. http://example.com/booking/ or http://example.com/
 * Below is an automatic address identification mechanism
 * if it fails, please comment it out, 
 * uncomment, the variable below and manually set the address
 */

//If this field is used, see line 110 in cfg.php and remove the slash before the filename and vice versa
$website_address = "WEBADDR";

/*
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
    $website_address = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
} else {
    $website_address = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}
*/

//Source email address for sending emails
$noreply = 'noreply@example.com';

//Values: true/false (DEBUG ONLY)
//Setting this to true, administrators will see system status notifications
$syserrors = true;
