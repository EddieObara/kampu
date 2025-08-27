<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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

    // Email content (Admin receives this)
    $mail->isHTML(true);
    $mail->Subject = !empty($subject) ? $subject : 'New Contact Form Submission';
    $mail->Body    = "
        <h3>You have a new message from your website contact form:</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Mobile:</b> {$mobile}</p>
        <p><b>Service:</b> {$service}</p> <!-- ✅ Service now included -->
        <p><b>Message:</b><br>{$message}</p>
    ";

    $mail->send();

    // ✅ AUTO-REPLY to sender
    $reply = new PHPMailer(true);
    $reply->isSMTP();
    $reply->Host       = 'smtp-relay.brevo.com';
    $reply->SMTPAuth   = true;
    $reply->Username   = getenv('SMTP_USERNAME'); 
    $reply->Password   = getenv('SMTP_PASSWORD'); 
    $reply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $reply->Port       = 587;

    // From company → to user
    $reply->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
    $reply->addAddress($email, $name);

    $reply->isHTML(true);
    $reply->Subject = "Thank you for contacting us!";
    $reply->Body    = "
        <p>Hi <b>{$name}</b>,</p>
        <p>Thank you for reaching out regarding <b>{$service}</b>. We’ve received your message and our team will get back to you shortly.</p>
        <p><b>Your Message:</b><br>{$message}</p>
        <p>Best regards,<br>" . getenv('SMTP_FROM_NAME') . "</p>
    ";

    $reply->send();

    // ✅ Redirect with success
    header("Location: $redirectPage?success=1");
    exit;

} catch (Exception $e) {
    header("Location: $redirectPage?error=" . urlencode($mail->ErrorInfo));
    exit;
}
?>
