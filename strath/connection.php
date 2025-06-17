<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "strathmore_ordering";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

// Create users table with all necessary columns
$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin', 'kitchen') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createUsersTable)) {
    // If table creation fails, try to add missing columns to existing table
    
    // Check if status column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
    if ($result && $result->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
    
    // Check if registration_date column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'registration_date'");
    if ($result && $result->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
}
?>