<?php
$host = "185.114.98.6";
$username = "Emily-773";
$password = "REAL_PASSWORD";
$database = "bookfinder";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
