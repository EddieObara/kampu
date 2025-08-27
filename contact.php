<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // === First Email (Client -> You) ===
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USERNAME');
        $mail->Password   = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
        $mail->addAddress(getenv('SMTP_TO'));
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Message from $name";
        $mail->Body    = "<h3>You have a new message from your website contact form</h3>
                          <p><strong>Name:</strong> $name</p>
                          <p><strong>Email:</strong> $email</p>
                          <p><strong>Message:</strong> $message</p>";

        $mail->send();

        // === Auto-Reply (You -> Client) ===
        $reply = new PHPMailer(true);

        // enable debugging for reply
        $reply->SMTPDebug  = 2; 
        $reply->Debugoutput = 'error_log';

        $reply->isSMTP();
        $reply->Host       = getenv('SMTP_HOST');
        $reply->SMTPAuth   = true;
        $reply->Username   = getenv('SMTP_USERNAME');
        $reply->Password   = getenv('SMTP_PASSWORD');
        $reply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $reply->Port       = 587;

        $reply->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
        $reply->addAddress($email, $name);

        $reply->isHTML(true);
        $reply->Subject = "Thank you for contacting us!";
        $reply->Body    = "<p>Hi $name,</p>
                           <p>Thanks for reaching out. We have received your message and will get back to you shortly.</p>
                           <p>Best regards,<br>Support Team</p>";

        $reply->send();

        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
    }
}
?>
