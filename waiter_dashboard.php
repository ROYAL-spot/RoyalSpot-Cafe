<?php
// -------------------------------
// Persistent session for 24 hours
// -------------------------------
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
session_start();

require 'db_connect.php';
date_default_timezone_set('Africa/Johannesburg');

// Auto-login via cookie if session expired
if(!isset($_SESSION['role']) && isset($_COOKIE['user_role'])){
    $_SESSION['user'] = $_COOKIE['user_name'];
    $_SESSION['role'] = $_COOKIE['user_role'];
}

// Only waiter can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'waiter'){
    header("Location: login.php");
    exit;
}

// Handle AJAX mark as served
if(isset($_POST['serve_ajax'])){
    $id = (int)$_POST['serve_id'];
    $result = $conn->query("UPDATE orders SET status='completed', updated_at=NOW() WHERE id=$id");
    echo json_encode(['success'=>$result?true:false,'error'=>$conn->error]);
    exit;
}

// Fetch ready orders
function fetchReadyOrders($conn){
    $orders = $conn->query("SELECT * FROM orders WHERE status='ready' AND DATE(created_at)=CURDATE() ORDER BY created_at ASC");
    $data=[];
    if($orders){
        while($row=$orders->fetch_assoc()){
            $data[]=$row;
        }
    }
    return $data;
}

// AJAX fetch
if(isset($_GET['fetch'])){
    echo json_encode(fetchReadyOrders($conn));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Waiter Dashboard</title>
<style>
body{font-family:Arial;background:#f5f5f5; padding:20px;}
.card{background:#fff;padding:15px;margin:10px;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
button{background:#28a745;color:#fff;padding:10px 15px;border:none;border-radius:6px;cursor:pointer;font-weight:bold;}
h1{text-align:center;}
</style>
</head>
<body>

<h1>🧑‍💼 Waiter Dashboard</h1>
<audio id="newOrderSound" src="ding.mp3" preload="auto"></audio>
<div id="waiter-orders"></div>

<script>
let lastReadyOrders = [];

// Flash effect for new orders
function flashNewOrder(element) {
    element.style.transition = "background 0.5s";
    element.style.background = "#ffff99"; // highlight color
    setTimeout(() => {
        element.style.background = "#fff"; // revert
    }, 1000);
}

// Load ready orders
function loadReadyOrders(){
    fetch('waiter_dashboard.php?fetch=1')
    .then(res=>res.json())
    .then(data=>{
        const container=document.getElementById('waiter-orders');
        container.innerHTML='';

        // Identify new ready orders
        const newOrders = data.filter(o => !lastReadyOrders.includes(o.id));
        if(newOrders.length > 0){
            document.getElementById('newOrderSound').play();
        }
        lastReadyOrders = data.map(o=>o.id);

        if(data.length===0){
            container.innerHTML="<p style='text-align:center; color:#888;'>No orders ready for pickup right now.</p>";
            return;
        }

        data.forEach(order=>{
            let createdAt = new Date(order.created_at);
            let mins = Math.floor((Date.now()-createdAt.getTime())/60000);
            let html=`<div class='card' id='order-${order.id}'>
                <h3>Order #${order.id}</h3>
                <p><strong>Table: ${order.table_num}</strong></p>
                <p>Items: ${order.items.replace(/\n/g,'<br>')}</p>
                <div class='time' data-time='${order.created_at}'>🕒 ${mins} min${mins!==1?'s':''} ago</div>
                <button onclick="markServed(${order.id})">Mark as Served</button>
            </div>`;
            container.innerHTML+=html;

            // Flash new orders
            if(newOrders.find(o=>o.id===order.id)){
                const el = document.getElementById(`order-${order.id}`);
                flashNewOrder(el);
            }
        });
    });
}

// Mark as served
function markServed(order_id){
    fetch('waiter_dashboard.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`serve_ajax=1&serve_id=${order_id}`
    }).then(res=>res.json())
      .then(resp=>{
        if(resp.success){
            loadReadyOrders();
        } else {
            alert('Error: '+resp.error);
        }
      });
}

// Update timers every 60s
setInterval(()=>{
    document.querySelectorAll('.time').forEach(el=>{
        const created = new Date(el.getAttribute('data-time'));
        const mins = Math.floor((Date.now()-created.getTime())/60000);
        el.innerHTML = `🕒 ${mins} min${mins!==1?'s':''} ago`;
    });
},60000);

// Refresh orders every 5s
setInterval(loadReadyOrders,5000);
loadReadyOrders();
</script>
</body>
</html>
