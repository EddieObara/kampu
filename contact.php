<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : "None selected";

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Send to site owner (you)
        $mail->setFrom($email, $name);
        $mail->addAddress('yourmainemail@example.com'); // your email

        $mail->isHTML(true);
        $mail->Subject = "New message from contact form: " . $subject;
        $mail->Body    = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Mobile:</strong> $mobile</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Services:</strong> $services</p>
            <p><strong>Message:</strong><br>$message</p>
        ";
        $mail->send();

        // Auto-reply to sender
        $autoReply = new PHPMailer(true);
        $autoReply->isSMTP();
        $autoReply->Host       = 'smtp-relay.brevo.com';
        $autoReply->SMTPAuth   = true;
        $autoReply->Username   = $_ENV['SMTP_USERNAME'];
        $autoReply->Password   = $_ENV['SMTP_PASSWORD'];
        $autoReply->SMTPSecure = 'tls';
        $autoReply->Port       = 587;

        $autoReply->setFrom('yourmainemail@example.com', 'Your Website');
        $autoReply->addAddress($email, $name);

        $autoReply->isHTML(true);
        $autoReply->Subject = "We received your message!";
        $autoReply->Body    = "
            <p>Hello <strong>$name</strong>,</p>
            <p>Thank you for reaching out to us. We’ve received your message and will get back to you as soon as possible.</p>
            <p><strong>Your message:</strong><br>$message</p>
            <p>Best regards,<br>Your Website Team</p>
        ";

        $autoReply->send();

        echo "Message has been sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
