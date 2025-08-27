<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$mobile  = $_POST['mobile'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';
$service = $_POST['service'] ?? '';

$redirectPage =<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);
    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USERNAME');
        $mail->Password   = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
        $mail->addAddress(getenv('SMTP_TO'));
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission";
        $mail->Body    = "<b>Name:</b> $name<br><b>Email:</b> $email<br><b>Message:</b><br>$message";

        $mail->send();

        // -------------------
        // AUTO-REPLY SECTION
        // -------------------
        $reply = new PHPMailer(true);
        try {
            $reply->isSMTP();
            $reply->Host       = getenv('SMTP_HOST');
            $reply->SMTPAuth   = true;
            $reply->Username   = getenv('SMTP_USERNAME');
            $reply->Password   = getenv('SMTP_PASSWORD');
            $reply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $reply->Port       = 587;

            $reply->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
            $reply->addAddress($email, $name); // Send back to client

            $reply->isHTML(true);
            $reply->Subject = "Thank you for contacting us!";
            $reply->Body    = "Hi $name,<br><br>We’ve received your message and will get back to you soon.<br><br>Best regards,<br>".getenv('SMTP_FROM_NAME');

            if (!$reply->send()) {
                error_log("Auto-reply error: " . $reply->ErrorInfo);
            }
        } catch (Exception $e) {
            error_log("Auto-reply exception: " . $e->getMessage());
        }

        echo "success";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
 $_SERVER['HTTP_REFERER'] ?? 'index.html'; 

$mail = new PHPMailer(true);

try {
    // --- SEND TO ADMIN ---
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME'); 
    $mail->Password   = getenv('SMTP_PASSWORD'); 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
    $mail->addAddress(getenv('SMTP_TO'));
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = !empty($subject) ? $subject : 'New Contact Form Submission';
    $mail->Body    = "
        <h3>You have a new message from your website contact form:</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Mobile:</b> {$mobile}</p>
        <p><b>Service:</b> {$service}</p>
        <p><b>Message:</b><br>{$message}</p>
    ";

    $mail->send();

    // --- AUTO-REPLY TO USER ---
    $reply = new PHPMailer(true);
    $reply->isSMTP();
    $reply->Host       = 'smtp-relay.brevo.com';
    $reply->SMTPAuth   = true;
    $reply->Username   = getenv('SMTP_USERNAME');
    $reply->Password   = getenv('SMTP_PASSWORD');
    $reply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $reply->Port       = 587;

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

    // 🔍 Debug logs (check PHP error_log)
    $reply->SMTPDebug = 2;
    $reply->Debugoutput = 'error_log';

    $reply->send();

    header("Location: $redirectPage?success=1");
    exit;

} catch (Exception $e) {
    header("Location: $redirectPage?error=" . urlencode($mail->ErrorInfo));
    exit;
}
?>
