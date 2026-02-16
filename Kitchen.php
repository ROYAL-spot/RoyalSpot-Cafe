<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$conn = new mysqli($_ENV['DB_SERVER'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME_ORDERS']);

if (isset($_POST['complete_id'])) {
    $id = (int)$_POST['complete_id'];
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $id");
}
$result = $conn->query("SELECT * FROM orders WHERE status = 'pending' AND DATE(created_at) = CURDATE() ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kitchen Live Dashboard</title>
    <meta http-equiv="refresh" content="30"> 
    <style>
        body { font-family: sans-serif; background: #1a1a1a; color: white; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .order-card { background: #333; border-top: 8px solid #007bff; padding: 15px; border-radius: 8px; }
        .order-header { font-weight: bold; font-size: 1.4rem; margin-bottom: 10px; border-bottom: 1px solid #444; }
        .done-btn { background: #28a745; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">👨‍🍳 ACTIVE KITCHEN ORDERS</h1>
    <div class="grid">
        <?php while($row = $result->fetch_assoc()): 
            $shortTable = str_replace('Table ', '', $row['table_num']); ?>
            <div class="order-card">
    <div class="order-header">Tbl<?php echo $shortTable; ?> #<?php echo $row['id']; ?></div>
    
    <div style="white-space: pre-wrap; margin-bottom: 10px;">
        <?php echo htmlspecialchars($row['items']); ?>
    </div>

    <?php if(!empty($row['notes'])): ?>
        <div style="background: #444; color: #ffcc00; padding: 8px; border-radius: 4px; font-style: italic; margin-top: 10px; border-left: 4px solid #ffcc00;">
            <strong>Note:</strong> <?php echo nl2br(htmlspecialchars($row['notes'])); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="complete_id" value="<?php echo $row['id']; ?>">
        <button type="submit" class="done-btn">MARK DONE</button>
    </form>
</div>
        <?php endwhile; ?>
    </div>
</body>
</html>