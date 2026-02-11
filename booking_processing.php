<?php
// 1. Show all errors so we stop the blank screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// 2. Load .env
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} else {
    die("STOP: .env file missing in this folder!");
}

// 3. Connect to Database (Using exact names from your .env)
$conn = new mysqli(
    $_ENV['DB_SERVER'], 
    $_ENV['DB_USERNAME'], 
    $_ENV['DB_PASSWORD'], 
    $_ENV['DB_NAME']
);

if ($conn->connect_error) {
    die("DATABASE CONNECTION FAILED: " . $conn->connect_error);
}

// 4. Capture Data (Fixed to match your HTML form names)
$name     = $_POST['customer-name'] ?? '';
$email    = $_POST['customer-email'] ?? '';
$phone    = $_POST['customer-phone'] ?? '';
$date     = $_POST['booking-date'] ?? '';
$time     = $_POST['booking-time'] ?? '';
$guests   = $_POST['number-of-guests'] ?? 1;
$smoking  = $_POST['smoking-preference'] ?? 'non-smoking';
$location = $_POST['table-location'] ?? 'indoor';

// 5. Insert into Database
$sql = "INSERT INTO reservations (customer_name, customer_email, customer_phone, booking_date, booking_time, number_of_guests, smoking_preference, table_location) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiss", $name, $email, $phone, $date, $time, $guests, $smoking, $location);

if ($stmt->execute()) {
    // 6. Send Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME']; 
        $mail->Password   = $_ENV['SMTP_PASSWORD']; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom($_ENV['SMTP_USERNAME'], 'RoyalSpot Cafe');
        $mail->addAddress($_ENV['SMTP_USERNAME']); 

        $mail->isHTML(true);
        $mail->Subject = "New Reservation: $name";
        $mail->Body    = "You have a new booking for $guests guests on $date at $time.";

        $mail->send();
        echo "<h1>Success!</h1><p>Your reservation is confirmed. See you soon!</p>";
    } catch (Exception $e) {
        // This tells us if the Gmail part failed but the database worked
        echo "<h1>Saved!</h1><p>Booking saved, but email notification failed. Error: {$mail->ErrorInfo}</p>";
    }
} else {
    echo "<h1>Error</h1><p>Database save failed: " . $stmt->error . "</p>";
}