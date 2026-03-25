<?php

session_start();

$csvPath = "./data/etudiants.csv";
$usersPath = "./data/users.csv";

if(!file_exists($usersPath) || !file_exists($csvPath)) {
  $userFile = fopen($usersPath, "w");
  $csvFile = fopen($csvPath, "w");
  $default_admin = [
    "admin",
     password_hash("admin", PASSWORD_DEFAULT),
    "admin"
  ];
  fputcsv($userFile, $default_admin);
  fclose($userFile);
  fclose($csvFile); 
}

if(isset($_SESSION['user'])) {
  header("Location: main/dashboard.php");
} else {
  header("Location: auth/login.php");
}

?>