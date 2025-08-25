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

// Check if form is submitted from index.html or contact.html
$redirectPage = $_SERVER['HTTP_REFERER'] ?? 'index.html'; 

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'loopandlogic6@gmail.com';  // Your Brevo sender email
    $mail->Password   = 'xsmtpsib-cab599c4e1153fe1c44586dea4723da0c2b48e1477a22c51838703e2bb39a2ce-RfxPysjSwFMvHAgT'; // Your Brevo SMTP Key
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('loopandlogic6@gmail.com', 'Website Contact Form');
    $mail->addAddress('loopandlogic6@gmail.com'); // Where you’ll receive the emails

    // Content
    $mail->isHTML(true);
    $mail->Subject = !empty($subject) ? $subject : 'New Contact Form Submission';
    $mail->Body    = "
        <h3>You have a new message from your website contact form:</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Mobile:</b> {$mobile}</p>
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
