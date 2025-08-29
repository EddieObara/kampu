<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $mobile  = htmlspecialchars($_POST['mobile']);
    $service = htmlspecialchars($_POST['service']);
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
        $mail->addAddress(getenv('SMTP_TO'));   // First recipient
        $mail->addAddress(getenv('SMTP_TOO'));  // Second recipient
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Message from $name";
        $mail->Body    = "<h3>You have a new message from your website contact form</h3>
                          <p><strong>Name:</strong> $name</p>
                          <p><strong>Email:</strong> $email</p>
                          <p><strong>Mobile:</strong> $mobile</p>
                          <p><strong>Service:</strong> $service</p>
                          <p><strong>Message:</strong> $message</p>";

        if (!$mail->send()) {
            throw new Exception("Mailer Error (Admin Copy): " . $mail->ErrorInfo);
        }

        // === Auto-Reply (You -> Client) ===
        $reply = new PHPMailer(true);

        // toggle debugging here if needed
        // $reply->SMTPDebug  = 2; 
        // $reply->Debugoutput = 'error_log';

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

        if (!$reply->send()) {
            throw new Exception("Mailer Error (Auto-Reply): " . $reply->ErrorInfo);
        }

        // ✅ Success response
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully!'
        ]);

    } catch (Exception $e) {
        // ❌ Error response
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage()
        ]);
    }
}
?>
