<?php
$host = "localhost";
$db_name = "game_api";   // must match what you created
$username = "root";      // default XAMPP username
$password = "";          // default XAMPP password is empty

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  die(json_encode(["error" => $e->getMessage()]));
}
?>
