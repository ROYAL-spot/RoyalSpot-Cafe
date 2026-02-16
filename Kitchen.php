<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'kitchen'){
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

if (isset($_POST['order_id'], $_POST['status'])) {
    $id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status', updated_at=NOW() WHERE id=$id");
}

function fetchOrders($conn, $status) {
    return $conn->query("SELECT * FROM orders 
                         WHERE status='$status' 
                         AND DATE(created_at)=CURDATE()
                         ORDER BY created_at ASC");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Kitchen Display</title>
<style>
body { background:#111; color:#fff; font-family:Arial; }
.section { margin:20px; }
.section h2 { border-bottom:2px solid #444; padding-bottom:5px; }
.grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:15px; }

.card { padding:15px; border-radius:10px; background:#1e1e1e; font-size:18px; }
.pending { border-top:8px solid orange; }
.preparing { border-top:8px solid #007bff; }
.ready { border-top:8px solid #28a745; }

.order-id { font-size:28px; color:#ff4444; font-weight:bold; }
.time { color:#ccc; font-size:14px; }

button { padding:8px 12px; margin:5px 3px; border:none; border-radius:6px; cursor:pointer; font-weight:bold; }
.prep-btn { background:#007bff; color:#fff; }
.ready-btn { background:#28a745; color:#fff; }
.done-btn { background:#555; color:#fff; }
</style>
</head>
<body>

<h1 style="text-align:center;">🍽 Kitchen Display System</h1>

<?php
$statuses = ['pending'=>'🟡 New Orders','preparing'=>'🔵 Preparing','ready'=>'🟢 Ready for Pickup'];

foreach ($statuses as $key => $title) {
    echo "<div class='section'><h2>$title</h2><div class='grid'>";
    $orders = fetchOrders($conn, $key);
    while($row = $orders->fetch_assoc()):
        $minutes = floor((time() - strtotime($row['created_at'])) / 60);
?>

<div class="card <?php echo $key; ?>">
    <div class="order-id">#<?php echo $row['id']; ?></div>
    <div>Table: <?php echo htmlspecialchars($row['table_number']); ?></div>
    <div class="time">⏱ <?php echo $minutes; ?> mins ago</div>
    <hr>
    <div><?php echo nl2br(htmlspecialchars($row['items'])); ?></div>

    <form method="POST">
        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
        <?php if($key=='pending'): ?>
            <button name="status" value="preparing" class="prep-btn">PREPARING</button>
        <?php elseif($key=='preparing'): ?>
            <button name="status" value="ready" class="ready-btn">READY</button>
        <?php elseif($key=='ready'): ?>
            <button name="status" value="completed" class="done-btn">DONE</button>
        <?php endif; ?>
    </form>
</div>

<?php endwhile; echo "</div></div>"; } ?>

<script>
setInterval(function(){ location.reload(); },15000);
</script>

</body>
</html>
