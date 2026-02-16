<?php
require 'db_connect.php';

// Generate REAL hashes on YOUR server
$adminHash = password_hash('admin123', PASSWORD_DEFAULT);
$kitchenHash = password_hash('kitchen123', PASSWORD_DEFAULT);
$waiterHash = password_hash('waiter123', PASSWORD_DEFAULT);

// Update your database
$conn->query("UPDATE users SET password = '$adminHash' WHERE username = 'admin'");
$conn->query("UPDATE users SET password = '$kitchenHash' WHERE username = 'kitchen1'");
$conn->query("UPDATE users SET password = '$waiterHash' WHERE username = 'waiter1'");

echo "Passwords updated successfully! Delete this file now.";
?>
