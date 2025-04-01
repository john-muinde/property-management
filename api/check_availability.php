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
        $_SESSION['message_type'] = "danger";
        header('Location: ../index.php');
        exit;
    }

    // Validate required fields
    $required = ['arrival_date', 'departure_date'];
    $errors = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }

    // Get form data
    $arrival_date = isset($_POST['arrival_date']) ? clean_input($_POST['arrival_date']) : '';
    $departure_date = isset($_POST['departure_date']) ? clean_input($_POST['departure_date']) : '';

    // Validate dates
    $today = date('Y-m-d');
    if ($arrival_date < $today) {
        $errors[] = "Arrival date cannot be in the past";
    }

    if ($departure_date <= $arrival_date) {
        $errors[] = "Departure date must be after arrival date";
    }

    // Check for available rooms
    if (empty($errors)) {
        // In a real application, this would query the database to find available rooms
        // For this example, we'll just redirect to the rooms page

        // Store dates in session for pre-filling forms
        $_SESSION['check_arrival'] = $arrival_date;
        $_SESSION['check_departure'] = $departure_date;
        $_SESSION['check_adults'] = isset($_POST['adults']) ? (int)$_POST['adults'] : 2;

        $_SESSION['message'] = "Rooms are available for your selected dates. Choose a room to book.";
        $_SESSION['message_type'] = "success";

        // Redirect to rooms page
        header('Location: ../rooms.php?check=availability');
        exit;
    } else {
        // Errors found
        $_SESSION['message'] = "Please correct the following errors: " . implode(", ", $errors);
        $_SESSION['message_type'] = "warning";
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
        exit;
    }
} else {
    // Not a POST request
    header('Location: ../index.php');
    exit;
}
