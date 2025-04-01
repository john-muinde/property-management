<?php
// Database connection for Lakeside Resorts and Spa
$db_host = '127.0.0.1';
$db_name = 'lakeside_resorts';
$db_user = 'root';
$db_pass = ''; // Change in production

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
