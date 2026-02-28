<?php

$servername = "localhost";
$username = "your_db_user";
$password = "your_db_password";
$database = "your_database_name";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>