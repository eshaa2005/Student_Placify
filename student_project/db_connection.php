<?php
$host = "127.0.0.1";  // MySQL Workbench runs locally
$username = "root";   // Default MySQL user
$password = "1234";     
$database = "student_records"; 

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
