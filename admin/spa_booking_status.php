<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Include database connection
require_once '../includes/connect.php';

// Check if ID and status are provided
if (
    !isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['status']) ||
    !in_array($_GET['status'], ['pending', 'confirmed', 'cancelled'])
) {
    $_SESSION['message'] = "Invalid request";
    $_SESSION['message_type'] = "danger";
    header('Location: spa.php');
    exit;
}

$booking_id = $_GET['id'];
$new_status = $_GET['status'];

// Update booking status
$sql = "UPDATE spa_bookings SET status = '$new_status' WHERE id = $booking_id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "Spa booking status updated to " . ucfirst($new_status);
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error updating booking status: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
}

// Redirect back to spa page
header('Location: spa.php');
exit;
