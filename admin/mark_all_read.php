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

// Update all unread messages to read
$sql = "UPDATE contact_submissions SET is_read = 1 WHERE is_read = 0";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = "All messages marked as read";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error marking messages as read: " . mysqli_error($conn);
    $_SESSION['message_type'] = "danger";
}

// Redirect back to messages page
header('Location: messages.php');
exit;
