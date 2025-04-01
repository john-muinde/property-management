<?php
// Helper functions for Lakeside Resorts and Spa

// Clean user input
function clean_input($data)
{
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
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

    $sql = "INSERT INTO room_bookings (room_id, guest_name, email, phone, arrival_date, departure_date, adults, children, special_requests) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "isssssiis",
        $booking_data['room_id'],
        $booking_data['guest_name'],
        $booking_data['email'],
        $booking_data['phone'],
        $booking_data['arrival_date'],
        $booking_data['departure_date'],
        $booking_data['adults'],
        $booking_data['children'],
        $booking_data['special_requests']
    );

    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return "Booking failed: " . mysqli_error($conn);
    }
}

// Check if email is subscribed
function is_subscribed($email)
{
    global $conn;

    $sql = "SELECT id FROM subscribers WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    return mysqli_stmt_num_rows($stmt) > 0;
}

// Generate a token for forms
function generate_token()
{
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['token'];
}
