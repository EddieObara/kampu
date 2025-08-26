<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Sanitize input
$name    = htmlspecialchars(trim($_POST['name'] ?? ''));
$email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$mobile  = htmlspecialchars(trim($_POST['mobile'] ?? ''));
$subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// Secure redirect
$redirectPage = $_SERVER['HTTP_REFERER'] ?? 'index.html';
$allowedPages = ['index.html', 'contact.html'];
$parsedUrl = parse_url($redirectPage, PHP_URL_PATH);
if (!in_array(basename($parsedUrl), $allowedPages)) {
    $redirectPage = 'index.html';
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME');
    $mail->Password   = getenv('SMTP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
    $mail->addAddress(getenv('SMTP_TO'));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mail->addReplyTo($email, $name);
    }

    $mail->isHTML(true);
    $mail->Subject = $subject ?: 'New Contact Form Submission';
    $mail->Body    = "
        <h3>New message from contact form:</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Mobile:</b> {$mobile}</p>
        <p><b>Subject:</b> {$subject}</p>
        <p><b>Message:</b><br>{$message}</p>
    ";
    $mail->AltBody = "Name: $name\nEmail: $email\nMobile: $mobile\nSubject: $subject\nMessage:\n$message";

    $mail->send();
    header("Location: $redirectPage?success=1");
    exit;
} catch (Exception $e) {
    header("Location: $redirectPage?error=" . urlencode($mail->ErrorInfo));
    exit;
}
?>
