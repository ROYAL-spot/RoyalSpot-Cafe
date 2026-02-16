<?php
    $orderNum = $_GET['orderNum'] ?? 'Error';
    $items    = $_GET['items'] ?? '';
    $notes    = $_GET['notes'] ?? '';
    $total    = $_GET['total'] ?? '0';
    
    // BUILD THE "PIC 2" WHATSAPP MESSAGE
    $waMsg = "*NEW ORDER: $orderNum*\n";
    $waMsg .= "--------------------------\n";
    $waMsg .= $items . "\n";
    
    if(!empty($notes)) {
        $waMsg .= "--------------------------\n";
        $waMsg .= "*Notes:* " . $notes . "\n";
    }
    
    $waMsg .= "--------------------------\n";
    $waMsg .= "*TOTAL: R$total*";

    $waLink = "https://wa.me/27817996763?text=" . rawurlencode($waMsg);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #f4f4f4; padding: 20px; text-align: center; }
        .receipt { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: inline-block; text-align: left; max-width: 400px; width: 100%; border-top: 8px solid #333; }
        .total { font-size: 1.5rem; font-weight: bold; text-align: right; font-family: sans-serif; }
        .wa-btn { background: #25D366; color: white; padding: 18px; text-decoration: none; border-radius: 10px; display: block; text-align: center; margin-top: 20px; font-weight: bold; font-family: sans-serif; }
        .modify { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; font-family: sans-serif; font-size: 0.9rem; border-bottom: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="receipt">
        <h2 style="text-align:center; font-family: sans-serif;">RoyalSpot Cafe</h2>
        <p style="text-align:center; font-weight:bold; color:#007bff; font-family: sans-serif;"><?php echo htmlspecialchars($orderNum); ?></p>
        <hr style="border: 1px dashed #eee;">
        
        <div style="white-space: pre-wrap; text-align: left; margin: 20px 0; font-size: 1.1rem;">
            <?php echo htmlspecialchars($items); ?>
        </div>

        <?php if(!empty($notes)): ?>
            <div style="text-align: left; background: #fff9e6; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <strong>Note:</strong> <?php echo htmlspecialchars($notes); ?>
            </div>
        <?php endif; ?>

        <hr style="border: 1px solid #333;">
        <div class="total">TOTAL: R<?php echo htmlspecialchars($total); ?></div>

        <a href="<?php echo $waLink; ?>" class="wa-btn">
            <i class="fab fa-whatsapp"></i> SEND TO WHATSAPP
        </a>
        
        <a href="menu.html" class="modify">Modify or Place New Order</a>
    </div>
</body>
</html>