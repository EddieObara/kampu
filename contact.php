<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// If browser sends a preflight OPTIONS request, just exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = htmlspecialchars($_POST['name']);
    $email    = htmlspecialchars($_POST['email']);
    $date     = htmlspecialchars($_POST['date']);
    $time     = htmlspecialchars($_POST['time']);
    $meeting  = htmlspecialchars($_POST['meeting']);
    $message  = htmlspecialchars($_POST['message']);

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
        if (getenv('SMTP_TOO')) {
            $mail->addAddress(getenv('SMTP_TOO'));
        }
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Appointment Request from $name";
        $mail->Body    = "<h3>New Appointment Request</h3>
                          <p><strong>Name:</strong> $name</p>
                          <p><strong>Email:</strong> $email</p>
                          <p><strong>Preferred Date:</strong> $date</p>
                          <p><strong>Preferred Time:</strong> $time</p>
                          <p><strong>Meeting Preference:</strong> $meeting</p>
                          <p><strong>Message:</strong> $message</p>";

        $mail->send();

        // === Auto-Reply (You -> Client) ===
        $reply = new PHPMailer(true);

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
        $reply->Subject = "Appointment Request Received";
     $reply->Body = '
  <div style="font-family: Arial, sans-serif; color:#333; margin:0; padding:0;">
    <!-- Letterhead -->
    <div style="text-align:center; background:#f5f5f5; ;">
      <img src="https://loopandlogic.dev/img/Heading.png" 
           alt="Loop & Logic Letterhead" 
           style="max-width: 500%; height:auto; text-align: center;">
    

 $reply->Body = '
  <div style="background:#f5f5f5; margin:0; padding:0; width:100%; font-family: Arial, sans-serif; color:#333;">
    
    <!-- Letterhead -->
    <div style="text-align:center; padding:20px 0;">
      <img src="https://loopandlogic.dev/img/Heading.png" 
           alt="Loop & Logic Letterhead" 
           style="max-width:100%; height:auto;">
    </div>

    <!-- Body -->
    <div style="background:#ffffff; padding:20px; margin: 0 auto; max-width:600px; border-radius:8px;">
      <p>Hi ' . $name . ',</p>
      <p>Thank you for booking an appointment. Here are the details we received:</p>
      <ul>
        <li><strong>Date:</strong> ' . $date . '</li>
        <li><strong>Time:</strong> ' . $time . '</li>
        <li><strong>Meeting Type:</strong> ' . $meeting . '</li>
      </ul>
      <p>We will confirm shortly. If you need to update your request, just reply to this email.</p>
      <p>Best regards,<br><strong>Loop & Logic Team</strong></p>
    </div>

    <!-- Footer -->
    <div style="background:#0d47a1; color:white; text-align:center; padding:15px; font-size:12px; margin-top:20px;">
      <p>Loop & Logic • Nairobi, Kenya<br>
      <a href="https://looplogic.co.ke" style="color:#fff; text-decoration:none;">www.looplogic.co.ke</a></p>
    </div>
  </div>';



        $reply->send();

        echo json_encode(['success' => true, 'message' => 'Appointment request sent successfully!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => "Mailer Error: {$mail->ErrorInfo}"]);
    }
}
?>
