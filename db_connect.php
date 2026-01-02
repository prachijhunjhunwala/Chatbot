<?php
$host = "localhost";  // default in XAMPP
$user = "root";       // default username
$pass = "";           // default password in XAMPP
$dbname = "chatbot_db";  // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
