<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'waiter'){
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

if(isset($_POST['serve_id'])){
    $id = (int)$_POST['serve_id'];
    $conn->query("UPDATE orders SET status='completed', updated_at=NOW() WHERE id=$id");
}

$readyOrders = $conn->query("SELECT * FROM orders 
                             WHERE status='ready' 
                             ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Waiter Dashboard</title>
<style>
body{font-family:Arial;background:#f5f5f5;}
.card{background:#fff;padding:15px;margin:10px;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
button{background:#28a745;color:#fff;padding:8px 12px;border:none;border-radius:6px;}
</style>
</head>
<body>

<h1>🧑‍💼 Waiter Dashboard</h1>

<?php while($row = $readyOrders->fetch_assoc()): ?>
<div class="card">
    <h3>Order #<?php echo $row['id']; ?></h3>
    <p>Table: <?php echo htmlspecialchars($row['table_number']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($row['items'])); ?></p>
    <form method="POST">
        <input type="hidden" name="serve_id" value="<?php echo $row['id']; ?>">
        <button>Mark as Served</button>
    </form>
</div>
<?php endwhile; ?>

</body>
</html>
