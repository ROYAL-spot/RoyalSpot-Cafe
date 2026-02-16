<?php
require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conn = new mysqli($_ENV['DB_SERVER'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME_ORDERS']);
if ($conn->connect_error) { die("Database Connection Failed"); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table_num'] ?? 'Unknown';
$items = $_POST['items'] ?? '';
$notes = $_POST['notes'] ?? ''; // <--- Must match the 'notes' key from JS
$total = $_POST['total'] ?? 0;

  $sql = "INSERT INTO orders (table_num, items, notes, total) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssd", $table, $items, $notes, $total);

    if ($stmt->execute()) {
        $orderId = $conn->insert_id; 
        $shortTable = str_replace('Table ', '', $table);
        $orderNum = "Tbl" . $shortTable . "#" . $orderId;
        header("Location: confirmation.php?orderNum=" . urlencode($orderNum) . "&total=$total&items=" . urlencode($items) . "&notes=" . urlencode($notes));
    }
}
?>