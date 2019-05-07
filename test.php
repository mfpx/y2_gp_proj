<?php
session_start();

require 'includes/loginHandler.php';
require 'includes/lib.php';
require 'includes/cfg.php';

$url = 'test.php';
$statusCode = '302';

echo 'session dump: ';
var_dump($_SESSION);
echo '<br>';

if ($_POST['logout']) {
    logout($url, $statusCode);
} elseif ($_POST['newuser']){
    $_SESSION['email'] = $_POST['newuser'];
}
?>
<br>options: <br><br>
<form action="test.php" method="post">
    <input type="submit" name="logout" value="logout"><br>
    <input type="text" name="newuser">
    <input type="submit" value="change user">
</form>
<form action="reset.php" method="post">
    <input type="text" name="email">
    <input type="submit" name="passreset" value="password reset email"><br>
</form>
