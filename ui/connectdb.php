<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php-pos";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception

} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

