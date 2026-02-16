<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

require 'db_connect.php';

$total = $conn->query("SELECT COUNT(*) as total FROM orders 
                       WHERE DATE(created_at)=CURDATE()")
              ->fetch_assoc()['total'];

$hourly = $conn->query("
SELECT HOUR(created_at) as hour, COUNT(*) as total
FROM orders
WHERE DATE(created_at)=CURDATE()
GROUP BY hour
ORDER BY hour ASC
");

$hours = [];
$counts = [];

while($row = $hourly->fetch_assoc()){
    $hours[] = $row['hour'];
    $counts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Analytics Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{font-family:Arial;background:#fafafa;}
.card{background:#fff;padding:20px;margin:20px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
</style>
</head>
<body>

<h1>📊 Restaurant Analytics</h1>

<div class="card">
    <h2>Total Orders Today</h2>
    <h1><?php echo $total; ?></h1>
</div>

<div class="card">
    <h2>Orders Per Hour</h2>
    <canvas id="ordersChart"></canvas>
</div>

<script>
const ctx = document.getElementById('ordersChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($hours); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: '#007bff'
        }]
    }
});
</script>

</body>
</html>
