<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../includes/connect.php';
require_once '../includes/functions.php';

// Initialize errors array and booking array
$errors = [];
$booking = [];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        // Validate required fields
        $required = ['room_id', 'guest_name', 'email', 'phone', 'arrival_date', 'departure_date'];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
            } else {
                $booking[$field] = $_POST[$field];
            }
        }

        // Optional fields
        $booking['adults'] = isset($_POST['adults']) ? (int) $_POST['adults'] : 1;
        $booking['children'] = isset($_POST['children']) ? (int) $_POST['children'] : 0;
        $booking['special_requests'] = isset($_POST['special_requests']) ? $_POST['special_requests'] : '';

        // Validate dates
        $today = date('Y-m-d');
        if (isset($booking['arrival_date']) && $booking['arrival_date'] < $today) {
            $errors[] = "Arrival date cannot be in the past";
        }

        if (
            isset($booking['arrival_date']) && isset($booking['departure_date']) &&
            $booking['departure_date'] <= $booking['arrival_date']
        ) {
            $errors[] = "Departure date must be after arrival date";
        }

        // Validate email
        if (isset($booking['email']) && !filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address";
        }

        // Check if room exists and is available
        if (isset($booking['room_id'])) {
            $room_id = $booking['room_id'];
            $sql = "SELECT * FROM rooms WHERE id = '$room_id' AND is_available = 1";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) === 0) {
                $errors[] = "The selected room is not available";
            }
        }

        // Check if dates conflict with existing bookings
        if (isset($booking['room_id']) && isset($booking['arrival_date']) && isset($booking['departure_date'])) {
            $room_id = $booking['room_id'];
            $arrival_date = $booking['arrival_date'];
            $departure_date = $booking['departure_date'];

            $sql = "SELECT * FROM room_bookings 
                    WHERE room_id = '$room_id' 
                    AND status != 'cancelled' 
                    AND ((arrival_date <= '$departure_date' AND departure_date >= '$arrival_date') 
                    OR (arrival_date <= '$departure_date' AND departure_date >= '$arrival_date') 
                    OR (arrival_date >= '$arrival_date' AND departure_date <= '$departure_date'))";

            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Sorry, this room is not available for the selected dates";
            }
        }
    }

    // Process booking if no errors
    if (empty($errors)) {
        // Replace the function call with direct SQL insertion
        $room_id = $booking['room_id'];
        $guest_name = $booking['guest_name'];
        $email = $booking['email'];
        $phone = $booking['phone'];
        $arrival_date = $booking['arrival_date'];
        $departure_date = $booking['departure_date'];
        $adults = $booking['adults'];
        $children = $booking['children'];
        $special_requests = $booking['special_requests'];
        $status = 'pending'; // Default status
        $booking_date = date('Y-m-d H:i:s'); // Current date and time

        $sql = "INSERT INTO room_bookings (room_id, guest_name, email, phone, arrival_date, departure_date, 
                adults, children, special_requests, status, booking_date) 
                VALUES ('$room_id', '$guest_name', '$email', '$phone', '$arrival_date', '$departure_date', 
                '$adults', '$children', '$special_requests', '$status', '$booking_date')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Thank you! Your booking request has been received successfully. We will contact you shortly to confirm your reservation.";
            $_SESSION['message_type'] = "success";
            header('Location: ../rooms.php?status=success');
            exit;
        } else {
            $_SESSION['message'] = "Booking failed: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
            header('Location: ../rooms.php?status=error');
            exit;
        }
    } else {
        // Store errors in session
        $_SESSION['errors'] = $errors;
        $_SESSION['message'] = "Please correct these errors: " . implode(", ", $errors);
        $_SESSION['message_type'] = "danger";
        header('Location: ../rooms.php?status=error');
        exit;
    }
} else {
    // Not a POST request
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
    header('Location: ../rooms.php');
    exit;
}