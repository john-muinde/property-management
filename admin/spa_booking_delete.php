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

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid request";
    $_SESSION['message_type'] = "danger";
    header('Location: spa.php');
    exit;
}

$booking_id = $_GET['id'];

// Delete booking
$sql = "DELETE FROM spa_bookings WHERE id = $booking_id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "Spa booking deleted successfully";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting booking: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
}

// Redirect back to spa page
header('Location: spa.php');
exit;
