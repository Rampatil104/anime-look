<?php
// contact_us.php - PHPMailer + Gmail SMTP (works on localhost & hosted servers)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

// ---- CONFIG ----
$receiver = 'proramgamer@gmail.com';        // Your Gmail to receive messages
$smtpUser = 'proramgamer@gmail.com';       // Gmail account used for SMTP
$smtpAppPassword = 'shrushti2010';    // <<-- Replace with your Gmail App Password
// ----------------

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? 'No subject');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    // Basic required fields check
    echo "Please complete Name, Email and Message.";
    exit;
}

// Simple sanitization (you can expand validation)
$nameSafe = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$emailSafe = filter_var($email, FILTER_VALIDATE_EMAIL);
$phoneSafe = filter_var($phone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$subjectSafe = filter_var($subject, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$messageSafe = htmlspecialchars($message);

// Build plain text body
$body = "You received a new message from your Anime Website:\n\n";
$body .= "Name: {$nameSafe}\n";
$body .= "Email: {$email}\n";
$body .= "Phone: {$phoneSafe}\n";
$body .= "Subject: {$subjectSafe}\n\n";
$body .= "Message:\n{$messageSafe}\n";

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpAppPassword;    // use App Password (required)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls'
    $mail->Port       = 587;

    // From & To
    // setFrom should be a valid sender — using SMTP account as From is best practice
    $mail->setFrom($smtpUser, 'OtakuSpot Contact Form'); 
    $mail->addReplyTo($email, $name);          // reply goes to submitter
    $mail->addAddress($receiver);

    // Content
    $mail->isHTML(false);
    $mail->Subject = "Contact form: {$subjectSafe}";
    $mail->Body    = $body;

    $mail->send();

    // Optional: also store to DB here if you want (not included)
    echo "Message Sent Successfully!";
} catch (Exception $e) {
    // For debugging on localhost you can echo $mail->ErrorInfo
    echo "Failed to send message. Mailer Error: " . $mail->ErrorInfo;
}
