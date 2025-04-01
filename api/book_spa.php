<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../includes/connect.php';
require_once '../includes/functions.php';

// Initialize errors array
$errors = [];
$booking = [];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        // Validate required fields
        $required = ['service_id', 'guest_name', 'email', 'phone', 'date', 'time'];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
            } else {
                $booking[$field] = clean_input($_POST[$field]);
            }
        }

        // Optional fields
        $booking['requests'] = isset($_POST['requests']) ? clean_input($_POST['requests']) : '';

        // Validate date
        $today = date('Y-m-d');
        if (isset($booking['date']) && $booking['date'] < $today) {
            $errors[] = "Booking date cannot be in the past";
        }

        // Validate email
        if (isset($booking['email']) && !filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address";
        }

        // Check if service exists and is available
        if (isset($booking['service_id'])) {
            $service_id = $booking['service_id'];
            $sql = "SELECT * FROM spa_services WHERE id = ? AND is_available = 1";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $service_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 0) {
                $errors[] = "The selected spa service is not available";
            }
        }

        // Check for time slot availability
        if (isset($booking['date']) && isset($booking['time']) && isset($booking['service_id'])) {
            $sql = "SELECT * FROM spa_bookings 
                    WHERE service_id = ? 
                    AND booking_date = ? 
                    AND booking_time = ? 
                    AND status != 'cancelled'";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "iss",
                $booking['service_id'],
                $booking['date'],
                $booking['time']
            );

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Sorry, this time slot is already booked. Please select another time.";
            }
        }
    }

    // Process booking if no errors
    if (empty($errors)) {
        $sql = "INSERT INTO spa_bookings (service_id, guest_name, email, phone, booking_date, booking_time, requests) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "issssss",
            $booking['service_id'],
            $booking['guest_name'],
            $booking['email'],
            $booking['phone'],
            $booking['date'],
            $booking['time'],
            $booking['requests']
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Thank you! Your spa treatment has been booked successfully.";
            $_SESSION['message_type'] = "success";
            header('Location: ../spa_services.php?status=success');
            exit;
        } else {
            $_SESSION['message'] = "Booking failed: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
            header('Location: ../spa_services.php?status=error');
            exit;
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['message'] = "Please correct these errors: " . implode(", ", $errors);
        $_SESSION['message_type'] = "danger";
        header('Location: ../spa_services.php?status=error');
        exit;
    }
} else {
    // Not a POST request
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
    header('Location: ../spa_services.php');
    exit;
}
