<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// $servername = "127.0.0.1";
// $username = "root";
// $password = ""; // Or your actual root password if set
// $database = "mdm_db";
// $port = 4306; // Custom MySQL port

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password =  $_ENV['DB_PASS']; // Or your actual root password if set
$database = $_ENV['DB_NAME'];
$port = $_ENV['DB_PORT']; // Custom MySQL port

$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
