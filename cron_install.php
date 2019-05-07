<!DOCTYPE html>
<html>
    <head>
        <title>Cron job installation</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        You are about to install a cron job into your personal crontab<br>
        This can also be installed into a system-wide crontab<br>
        Please refer to the manual for backup instructions, and crontab differences!<br><br>
        Please enter <b>yes</b> below, as a confirmation of this action<br>
        <b>Note: </b>This will overwrite any existing personal cron jobs<br>
        <form action="cron_install.php" method="post">
            <input type="text" name="confirmation">
            <input type="submit" value="Continue">
        </form>
    </body>
</html>
<?php

/*
 * CS22220 Group 3
 * Web booking system
 * cron_install.php developed by David C.
 */

$dir = getcwd();
$file_path = $dir . "/includes/cron.php";
//this assumes that php binary is located in /usr/bin/php
$cron_text = "0 1 * * * /usr/bin/php $file_path >/dev/null 2>&1\n";

if ($_POST['confirmation'] == 'yes') {
    install($cron_text, $dir);
}

function install($cron_text, $dir) {
    $newfile = fopen("cronfile", "w") or die("Unable to open file!");
    fwrite($newfile, $cron_text);
    fclose($newfile);

//just a precaution on improperly configured systems;
    chmod("cronfile", 0644);

    echo 'Done!<br><br>';
    echo 'Please open the terminal on the system where this website is hosted, and type <i>cd ' . $dir . '</i><br>';
    echo 'Then type "<i>crontab cronfile</i>"<br><br>';
    echo 'You should now delete this file for security reasons!';
}
