<?php
// ============================================
// CONTACT FORM API (IMPROVED VERSION)
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include files
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/email.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Invalid request method');
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

// Validate required fields
$required = ['name', 'email', 'message'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        jsonResponse('error', "Missing required field: $field");
    }
}

// Sanitize inputs
$name = sanitizeInput($input['name']);
$email = sanitizeInput($input['email']);
$phone = isset($input['phone']) ? sanitizeInput($input['phone']) : '';
$subject = isset($input['subject']) ? sanitizeInput($input['subject']) : 'General Inquiry';
$message = sanitizeInput($input['message']);

// Validate email
if (!validateEmail($email)) {
    jsonResponse('error', 'Invalid email address');
}

try {
    $conn = getConnection();

    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {

        // Send emails (optional safe check)
        if (function_exists('sendContactConfirmation')) {
            sendContactConfirmation($email, $name, $message);
        }

        if (function_exists('sendAdminContactNotification')) {
            sendAdminContactNotification($name, $email, $message);
        }

        // Log activity
        if (function_exists('logActivity')) {
            logActivity('CONTACT_SUBMITTED', "Contact from $name ($email)");
        }

        jsonResponse('success', 'Message sent successfully! We will respond within 24 hours.');

    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    jsonResponse('error', 'Server error: ' . $e->getMessage());
}
?>