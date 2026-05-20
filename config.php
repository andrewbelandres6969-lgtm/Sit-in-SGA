<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sit_in_sga';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
