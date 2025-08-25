<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Collect form data safely
$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$mobile  = $_POST['mobile'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Determine which page the form was submitted from
$redirectPage = $_SERVER['HTTP_REFERER'] ?? 'index.html'; 

$mail = new PHPMailer(true);

try {
    // SMTP settings (Brevo)
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME'); // Brevo SMTP login: 958a61001@smtp-brevo.com
    $mail->Password   = getenv('SMTP_PASSWORD'); // Brevo SMTP key
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME')); // e.g., loopandlogic6@gmail.com, Website Contact Form
    $mail->addAddress(getenv('SMTP_TO')); // recipient email
    $mail->addReplyTo($email, $name);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = !empty($subject) ? $subject : 'New Contact Form Submission';
    $mail->Body    = "
        <h3>You have a new message from your website contact form:</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Mobile:</b> {$mobile}</p>
        <p><b>Subject:</b> {$subject}</p>
        <p><b>Message:</b><br>{$message}</p>
        
    ";

    $mail->send();

    // Redirect back to the page the form was submitted from
    header("Location: $redirectPage?success=1");
    exit;

} catch (Exception $e) {
    // Redirect back with PHPMailer error
    header("Location: $redirectPage?error=" . urlencode($mail->ErrorInfo));
    exit;
}
?>
