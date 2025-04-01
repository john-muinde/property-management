<?php

/**
 * Contact Form API Endpoint
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../includes/connect.php';
require_once '../includes/functions.php';

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Invalid request, please try again.';
        $_SESSION['contact_response'] = $response;
        header('Location: ../contact.php');
        exit;
    }

    // Validate required fields
    $required_fields = ['name', 'email', 'message'];
    $contact_data = [];
    $has_errors = false;

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $response['errors'][] = ucfirst($field) . ' is required.';
            $has_errors = true;
        } else {
            $contact_data[$field] = sanitize_input($_POST[$field]);
        }
    }

    // Optional fields
    $contact_data['phone'] = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';

    // Validate email format
    if (isset($contact_data['email']) && !filter_var($contact_data['email'], FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = 'Please enter a valid email address.';
        $has_errors = true;
    }

    // If no validation errors, save to database
    if (!$has_errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, phone, message) 
                                  VALUES (:name, :email, :phone, :message)");

            $stmt->bindParam(':name', $contact_data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $contact_data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':phone', $contact_data['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':message', $contact_data['message'], PDO::PARAM_STR);

            $stmt->execute();

            $response['success'] = true;
            $response['message'] = 'Thank you for your message! Our team will contact you shortly.';
        } catch (PDOException $e) {
            error_log('Contact Form Error: ' . $e->getMessage());
            $response['message'] = 'An error occurred. Please try again later.';
        }
    } else {
        $response['message'] = 'Please correct the errors in your submission.';
    }

    // Store response in session for redirect
    $_SESSION['contact_response'] = $response;

    // Redirect back to contact page
    if ($response['success']) {
        header('Location: ../contact.php?contact=success');
    } else {
        header('Location: ../contact.php?contact=error');
    }
    exit;
} else {
    // Not a POST request
    $response['message'] = 'Invalid request method.';
    $_SESSION['contact_response'] = $response;
    header('Location: ../contact.php');
    exit;
}
