<?php
// ============================================
// SUBMIT STUDENT INQUIRY API
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/email.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Invalid request method');
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// If no JSON, try regular POST
if (!$input) {
    $input = $_POST;
}

// Validate required fields
$required = ['name', 'email', 'interest'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        jsonResponse('error', "Missing required field: $field");
    }
}

$name = sanitizeInput($input['name']);
$email = sanitizeInput($input['email']);
$interest = sanitizeInput($input['interest']);
$phone = isset($input['phone']) ? sanitizeInput($input['phone']) : '';
$country = isset($input['country']) ? sanitizeInput($input['country']) : '';
$education_level = isset($input['education_level']) ? sanitizeInput($input['education_level']) : '';
$message = isset($input['message']) ? sanitizeInput($input['message']) : '';
$referral = isset($input['referral']) ? sanitizeInput($input['referral']) : '';

// Validate email
if (!validateEmail($email)) {
    jsonResponse('error', 'Invalid email address');
}

// Save to database
$conn = getConnection();

$sql = "INSERT INTO student_inquiries (full_name, email, phone, country, education_level, interest_type, message, referral_source, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $name, $email, $phone, $country, $education_level, $interest, $message, $referral);

if ($stmt->execute()) {
    $inquiryId = $stmt->insert_id;
    
    // Send confirmation email to student
    sendStudentConfirmation($email, $name);
    
    // Send notification email to admin
    sendAdminNotification($name, $email, $interest, $message);
    
    // Log activity
    logActivity('INQUIRY_SUBMITTED', "New inquiry from $name ($email)");
    
    jsonResponse('success', 'Thank you! Our experts will contact you within 24 hours.', [
        'id' => $inquiryId
    ]);
} else {
    logActivity('INQUIRY_ERROR', "Failed to save inquiry: " . $conn->error);
    jsonResponse('error', 'Database error. Please try again later.');
}

$stmt->close();
$conn->close();
?>