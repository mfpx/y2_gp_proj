<?php
/*
 * CS22220 Group 3
 * Web booking system
 * load.php developed by David C, Ysabel W. and Gethin L.
 */

require 'includes/cfg.php';

$connect = new PDO("mysql:host=$host;dbname=$database", $username , $password);

$data = array();
$query = "SELECT * FROM bookings ORDER BY id";

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

foreach($result as $row)
{
 $data[] = array(
  'id'   => $row["id"],
  'title'   => $row["room"],
  'start'   => $row["date"],
  'cat'   => $row["cat"],
  'end'   => $row["date"], 
  'em' => $row ["email"], 
 );
}

echo json_encode($data);

