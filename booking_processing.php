<?php
// 1. Error Reporting
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
    die("STOP: .env file missing!");
}

// 3. Connect to Database
$conn = new mysqli(
    $_ENV['DB_SERVER'],   // localhost
    $_ENV['DB_USERNAME'], // root
    $_ENV['DB_PASSWORD'], // (blank)
    $_ENV['DB_NAME']      // royalspot_cafe
);

if ($conn->connect_error) {
    die("DATABASE CONNECTION FAILED: " . $conn->connect_error);
}

// 4. Capture and Validate Date
$date = $_POST['booking-date'] ?? ''; // FIXED: Changed variable name to match usage below

if (!empty($date)) {
    $today = new DateTime(); 
    $booking_date = new DateTime($date);
    
    // Calculate difference
    $interval = $today->diff($booking_date);
    $days_ahead = (int)$interval->format('%r%a');

    if ($days_ahead < 2) {
        echo "<script>
                alert('Reservations must be made at least 2 days in advance. Please select a later date.');
                window.history.back();
              </script>";
        exit;
    }
}

// Capture other fields
$name     = $_POST['customer-name'] ?? '';
$email    = $_POST['customer-email'] ?? '';
$phone    = $_POST['customer-phone'] ?? '';
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
    
    // 6. Send Confirmation Email to Management
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
        $mail->Subject = "New Reservation Alert: $name";
        $mail->Body    = "<h3>New Booking Received</h3>
                          <p><strong>Name:</strong> $name</p>
                          <p><strong>Email:</strong> $email</p>
                          <p><strong>Phone:</strong> $phone</p>
                          <p><strong>Guests:</strong> $guests</p>
                          <p><strong>Date:</strong> $date at $time</p>
                          <p><strong>Preference:</strong> $smoking, $location</p>";

        $mail->send();
        
        // Redirect to a success page
        header("Location: index.html?status=success");
        exit;

    } catch (Exception $e) {
        // Even if email fails, the DB record is saved.
        header("Location: index.html?status=db_saved_email_error");
        exit;
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>