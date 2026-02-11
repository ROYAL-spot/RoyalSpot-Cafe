<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'somiladyantyi17@gmail.com'; // Use actual email
    $mail->Password = 'bxvamjiluddkhrju'; // Use actual App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('somiladyantyi17@gmail.com', 'Test Mail');
    $mail->addAddress('somiladyantyi17@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "Test Email";
    $mail->Body = "<h2>This is a test email</h2>";

    if ($mail->send()) {
        echo "✅ Test email sent successfully!";
    } else {
        echo "❌ Failed to send email.";
    }
} catch (Exception $e) {
    echo "Error: " . $mail->ErrorInfo;
}
?>
