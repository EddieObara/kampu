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
$service = $_POST['service'] ?? ''; // ✅ New field added

// Determine which page the form was submitted from
$redirectPage = $_SERVER['HTTP_REFERER'] ?? 'index.html'; 

$mail = new PHPMailer(true);

try {
    // SMTP settings (Brevo)
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME'); 
    $mail->Password   = getenv('SMTP_PASSWORD'); 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
    $mail->addAddress(getenv('SMTP_TO'));
    $mail->addReplyTo($email, $name);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = !empty($subject) ? $subject : 'New Contact Form Submission';
    $mail->Body    = "
        <h3>You have a new message from your website contact form:</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Mobile:</b> {$mobile}</p>
        <p><b>Service:</b> {$service}</p> <!-- ✅ Service now included -->
        <p><b>Subject:</b> {$subject}</p>
        <p><b>Message:</b><br>{$message}</p>
    ";

    $mail->send();

    header("Location: $redirectPage?success=1");
    exit;

} catch (Exception $e) {
    header("Location: $redirectPage?error=" . urlencode($mail->ErrorInfo));
    exit;
}
?>
