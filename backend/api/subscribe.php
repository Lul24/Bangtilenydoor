<?php
// ============================================
// NEWSLETTER SUBSCRIPTION API
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Invalid request method');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

if (empty($input['email'])) {
    jsonResponse('error', 'Email address is required');
}

$email = sanitizeInput($input['email']);

if (!validateEmail($email)) {
    jsonResponse('error', 'Invalid email address');
}

$conn = getConnection();

// Check if email already exists
$checkStmt = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    jsonResponse('info', 'You are already subscribed to our newsletter!');
} else {
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        logActivity('NEWSLETTER_SUBSCRIBE', "New subscriber: $email");
        jsonResponse('success', 'Successfully subscribed to our newsletter!');
    } else {
        jsonResponse('error', 'Subscription failed. Please try again.');
    }
    $stmt->close();
}

$checkStmt->close();
$conn->close();
?>