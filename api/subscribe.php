<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../includes/connect.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        $_SESSION['subscribe_message'] = "Invalid request. Please try again.";
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
        exit;
    }

    // Validate email
    if (empty($_POST['email'])) {
        $_SESSION['subscribe_message'] = "Email address is required.";
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
        exit;
    }

    $email = clean_input($_POST['email']);

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['subscribe_message'] = "Please enter a valid email address.";
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
        exit;
    }

    // Check if already subscribed
    if (is_subscribed($email)) {
        $_SESSION['subscribe_message'] = "You are already subscribed to our newsletter.";
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
        exit;
    }

    // Add to subscribers table
    $sql = "INSERT INTO subscribers (email) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['subscribe_message'] = "Thank you for subscribing to our newsletter!";
    } else {
        $_SESSION['subscribe_message'] = "An error occurred. Please try again later.";
    }

    mysqli_stmt_close($stmt);

    // Redirect back
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
    exit;
} else {
    // Not a POST request
    header('Location: ../index.php');
    exit;
}
