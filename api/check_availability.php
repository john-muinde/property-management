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
        // Query to find booked rooms in the date range
        $sql = "SELECT DISTINCT room_id FROM room_bookings 
                WHERE status != 'cancelled' AND (
                    (arrival_date <= ? AND departure_date >= ?) 
                    OR (arrival_date <= ? AND departure_date >= ?) 
                    OR (arrival_date >= ? AND departure_date <= ?)
                )";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "ssssss",
            $departure_date,
            $arrival_date,
            $departure_date,
            $arrival_date,
            $arrival_date,
            $departure_date
        );

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Get booked room IDs
        $booked_rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $booked_rooms[] = $row['room_id'];
        }

        // Get all available rooms
        $available_rooms_sql = "SELECT id FROM rooms WHERE is_available = 1";
        if (!empty($booked_rooms)) {
            $available_rooms_sql .= " AND id NOT IN (" . implode(',', $booked_rooms) . ")";
        }

        $available_rooms_result = mysqli_query($conn, $available_rooms_sql);
        $available_rooms = [];

        if ($available_rooms_result && mysqli_num_rows($available_rooms_result) > 0) {
            while ($row = mysqli_fetch_assoc($available_rooms_result)) {
                $available_rooms[] = $row['id'];
            }
        }

        // Store dates in session for pre-filling forms
        $_SESSION['check_arrival'] = $arrival_date;
        $_SESSION['check_departure'] = $departure_date;
        $_SESSION['check_adults'] = isset($_POST['adults']) ? (int)$_POST['adults'] : 2;

        if (empty($available_rooms)) {
            $_SESSION['message'] = "Sorry, no rooms are available for your selected dates.";
            $_SESSION['message_type'] = "warning";
        } else {
            $_SESSION['available_rooms'] = $available_rooms;
            $_SESSION['message'] = "We found " . count($available_rooms) . " room(s) available for your selected dates.";
            $_SESSION['message_type'] = "success";
        }

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
