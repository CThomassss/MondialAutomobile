<?php
// Database configuration
$host = 'localhost'; // Hostname (usually localhost)
$username = 'root'; // Your database username
$password = ''; // Your database password (leave empty if none)
$database = 'mondialautomobile'; // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful
// echo "Connected successfully"; // Uncomment for debugging
?>
