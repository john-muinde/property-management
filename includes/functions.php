<?php
// Helper functions for Lakeside Resorts and Spa

// Clean user input - not needed for direct SQL approach
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data; // Removed mysqli_real_escape_string
}

// Format price with $ sign
function format_price($price)
{
    return 'Kshs. ' . number_format($price, 2);
}

// Get all available rooms
function get_rooms()
{
    global $conn;
    $rooms = [];

    $sql = "SELECT * FROM rooms WHERE is_available = 1 ORDER BY price";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = $row;
        }
    }

    return $rooms;
}

// Get all spa services
function get_spa_services()
{
    global $conn;
    $services = [];

    $sql = "SELECT * FROM spa_services WHERE is_available = 1 ORDER BY price";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
    }

    return $services;
}

// Create a new room booking
function create_room_booking($booking_data)
{
    global $conn;

    $room_id = $booking_data['room_id'];
    $guest_name = $booking_data['guest_name'];
    $email = $booking_data['email'];
    $phone = $booking_data['phone'];
    $arrival_date = $booking_data['arrival_date'];
    $departure_date = $booking_data['departure_date'];
    $adults = $booking_data['adults'];
    $children = $booking_data['children'];
    $special_requests = $booking_data['special_requests'];

    $sql = "INSERT INTO room_bookings (room_id, guest_name, email, phone, arrival_date, departure_date, adults, children, special_requests) 
            VALUES ('$room_id', '$guest_name', '$email', '$phone', '$arrival_date', '$departure_date', '$adults', '$children', '$special_requests')";

    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return "Booking failed: " . mysqli_error($conn);
    }
}

// Generate a token for forms
function generate_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}