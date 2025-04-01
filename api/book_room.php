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
        $_SESSION['message'] = "Invalid request. Please try again.";
        header('Location: ../rooms.php');
        exit;
    }

    // Validate required fields
    $required = ['room_id', 'guest_name', 'email', 'phone', 'arrival_date', 'departure_date'];
    $errors = [];
    $booking = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        } else {
            $booking[$field] = clean_input($_POST[$field]);
        }
    }

    // Optional fields
    $booking['adults'] = isset($_POST['adults']) ? (int)$_POST['adults'] : 1;
    $booking['children'] = isset($_POST['children']) ? (int)$_POST['children'] : 0;
    $booking['special_requests'] = isset($_POST['special_requests']) ? clean_input($_POST['special_requests']) : '';

    // Validate dates
    $today = date('Y-m-d');
    if ($booking['arrival_date'] < $today) {
        $errors[] = "Arrival date cannot be in the past";
    }

    if ($booking['departure_date'] <= $booking['arrival_date']) {
        $errors[] = "Departure date must be after arrival date";
    }

    // Validate email
    if (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    // Process booking if no errors
    if (empty($errors)) {
        $result = create_room_booking($booking);

        if ($result === true) {
            $_SESSION['message'] = "Thank you! Your booking request has been received successfully. We will contact you shortly to confirm your reservation.";
            header('Location: ../rooms.php?status=success');
            exit;
        } else {
            $_SESSION['message'] = $result;
            header('Location: ../rooms.php?status=error');
            exit;
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['message'] = "Please correct the errors and try again.";
        header('Location: ../rooms.php?status=error');
        exit;
    }
} else {
    // Not a POST request
    header('Location: ../rooms.php');
    exit;
}
