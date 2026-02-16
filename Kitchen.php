<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'kitchen'){
    header("Location: login.php");
    exit;
}

require 'db_connect.php';
date_default_timezone_set('Africa/Johannesburg');

// Handle AJAX status updates
if(isset($_POST['ajax_update'])){
    $id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $result = $conn->query("UPDATE orders SET status='$status', updated_at=NOW() WHERE id=$id");
    echo json_encode(['success'=>$result?true:false, 'error'=>$conn->error]);
    exit;
}

// Fetch orders by status
function fetchOrders($conn, $status) {
    $status = $conn->real_escape_string($status);
    $orders = $conn->query("SELECT * FROM orders WHERE status='$status' AND DATE(created_at)=CURDATE() ORDER BY created_at ASC");
    $data = [];
    if($orders){
        while($row = $orders->fetch_assoc()){
            $data[] = $row;
        }
    }
    return $data;
}

// AJAX fetch for JS
if(isset($_GET['fetch'])){
    $response = [];
    foreach(['pending','preparing','ready'] as $status){
        $response[$status] = fetchOrders($conn,$status);
    }
    echo json_encode($response);
    exit;
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
.pending { border-left: 5px solid #ffc107; }
.preparing { border-left: 5px solid #17a2b8; }
.ready { border-left: 5px solid #28a745; }
.order-id { font-weight:bold; color:#888; margin-bottom:10px; }
.time { font-size:14px; color:#aaa; margin-top:5px; }
button { width:100%; padding:10px; margin-top:10px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
.prep-btn { background:#ffc107; color:#000; }
.ready-btn { background:#17a2b8; color:#fff; }
</style>
</head>
<body>

<h1 style="text-align:center;">🍳 Kitchen Display System</h1>
<div id="kitchen-sections"></div>

<script>
// Status mapping
const statuses = {
    'pending': '🆕 New Orders',
    'preparing': '👨‍🍳 Preparing',
    'ready': '✅ Ready for Pickup'
};

function loadOrders() {
    fetch('kitchen.php?fetch=1')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('kitchen-sections');
        container.innerHTML = '';
        for(let key in statuses){
            let html = `<div class='section'><h2>${statuses[key]}</h2><div class='grid'>`;
            if(data[key].length === 0){
                html += "<div style='color:#888;'>No orders</div>";
            }
            data[key].forEach(order => {
                let createdAt = new Date(order.created_at);
                let minutes = Math.floor((Date.now()-createdAt.getTime())/60000);
                html += `<div class='card ${key}'>
                    <div class='order-id'>#${order.id}</div>
                    <div>Table: ${order.table_num}</div>
                    <div class='time' data-time='${order.created_at}'>🕒 ${minutes} min${minutes!==1?'s':''} ago</div><hr>
                    <div>${order.items.replace(/\n/g,"<br>")}</div>`;
                if(key==='pending'){
                    html += `<button onclick="updateStatus(${order.id},'preparing')" class='prep-btn'>START PREPARING</button>`;
                } else if(key==='preparing'){
                    html += `<button onclick="updateStatus(${order.id},'ready')" class='ready-btn'>READY</button>`;
                } else if(key==='ready'){
                    html += `<div style="color:#28a745; font-weight:bold; margin-top:10px; text-align:center;">✔ Waiting for Waiter...</div>`;
                }
                html += `</div>`;
            });
            html += '</div></div>';
            container.innerHTML += html;
        }
    });
}

function updateStatus(order_id, status){
    fetch('kitchen.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `ajax_update=1&order_id=${order_id}&status=${status}`
    }).then(res => res.json())
      .then(resp => {
        if(resp.success){
            loadOrders();
        } else {
            alert('Error: '+resp.error);
        }
      });
}

// Update timers every 60 seconds
setInterval(()=>{
    document.querySelectorAll('.time').forEach(el=>{
        const created = new Date(el.getAttribute('data-time'));
        const mins = Math.floor((Date.now()-created.getTime())/60000);
        el.innerHTML = `🕒 ${mins} min${mins!==1?'s':''} ago`;
    });
},60000);

setInterval(loadOrders,5000);
loadOrders();
</script>
</body>
</html>
