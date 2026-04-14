<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

// ✅ YOUR EMAIL CONFIG
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'bangtilenydooroffice@gmail.com');
define('SMTP_PASS', 'oesm kgft qohp afle'); // 🔥 PUT YOUR APP PASSWORD
define('SMTP_PORT', 587);

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, 'Bangtilenydoor Academy');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

// 🎓 STUDENT EMAIL (Inquiry Form)
function sendStudentConfirmation($email, $name) {
    $subject = "Application Received";

    $body = "
    <h2>Hello $name,</h2>
    <p>Your application has been received successfully.</p>
    <p>We will contact you within 24 hours.</p>
    <br>
    <p>Regards,<br>Bangtilenydoor Academy</p>
    ";

    return sendEmail($email, $subject, $body);
}

// ADMIN EMAIL (Inquiry Form)
function sendAdminNotification($name, $email, $interest, $message) {
    $subject = " New Inquiry";

    $body = "
    <h3>New Student Inquiry</h3>
    <p><b>Name:</b> $name</p>
    <p><b>Email:</b> $email</p>
    <p><b>Interest:</b> $interest</p>
    <p><b>Message:</b> $message</p>
    ";

    return sendEmail(SMTP_USER, $subject, $body);
}

/* ============================================
CONTACT FORM EMAILS (FIX FOR CONTACT API)
============================================ */

// SEND CONFIRMATION TO USER (Contact Form)
function sendContactConfirmation($email, $name, $message) {
    $subject = "We received your message";

    $body = "
    <h2>Hello $name,</h2>
    <p>Thank you for contacting Bangtilenydoor Academy.</p>
    <p>We have received your message and will respond within 24 hours.</p>

    <hr>
    <p><b>Your Message:</b></p>
    <p>$message</p>

    <br>
    <p>Best regards,<br>Bangtilenydoor Academy Team</p>
    ";

    return sendEmail($email, $subject, $body);
}

// SEND NOTIFICATION TO ADMIN (Contact Form)
function sendAdminContactNotification($name, $email, $message) {
    $subject = "New Contact Message";

    $body = "
    <h3>New Contact Form Submission</h3>
    <p><b>Name:</b> $name</p>
    <p><b>Email:</b> $email</p>
    <p><b>Message:</b></p>
    <p>$message</p>
    ";

    return sendEmail(SMTP_USER, $subject, $body);
}
?>