<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "12345678";
$database = "strathmore_ordering";

// Create connection directly to the intended database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");
?>
