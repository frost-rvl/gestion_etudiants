<?php

session_start();

$usersPath = "./data/users.csv";
if(!file_exists($usersPath)) {
  $file = fopen($usersPath, "w");
  $data = [
    "admin",
     password_hash("admin", PASSWORD_DEFAULT),
    "admin"
  ];
  fputcsv($file, $data);
  fclose($file);
}

if(isset($_SESSION['user'])) {
  header("Location: main/dashboard.php");
} else {
  header("Location: auth/login.php");
}

?>