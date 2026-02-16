<?php
// -------------------------------
// Persistent session for 24 hours
// -------------------------------
ini_set('session.gc_maxlifetime', 86400);       // server session lifetime
session_set_cookie_params(86400);               // browser cookie lifetime
session_start();

require 'db_connect.php';
date_default_timezone_set('Africa/Johannesburg');

// ---------- Auto-login via cookies ----------
if(!isset($_SESSION['role']) && isset($_COOKIE['user_id']) && isset($_COOKIE['user_role']) && isset($_COOKIE['user_name'])){
    $_SESSION['user'] = $_COOKIE['user_name'];
    $_SESSION['role'] = $_COOKIE['user_role'];

    // Redirect to proper dashboard
    if($_SESSION['role'] == 'admin') header("Location: analytics_dashboard.php");
    elseif($_SESSION['role'] == 'kitchen') header("Location: kitchen.php");
    else header("Location: waiter_dashboard.php");
    exit;
}

// ---------- Handle login form ----------
if(isset($_POST['login'])){
    $username = $conn->real_escape_string($_POST['username']);
    $password_input = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        if(password_verify($password_input, $user['password'])){
            $_SESSION['user'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Set cookies for auto-login (24 hours)
            setcookie("user_id", $user['id'], time()+86400, "/");
            setcookie("user_name", $user['full_name'], time()+86400, "/");
            setcookie("user_role", $user['role'], time()+86400, "/");

            // Redirect to proper dashboard
            if($user['role'] == 'admin') header("Location: analytics_dashboard.php");
            elseif($user['role'] == 'kitchen') header("Location: kitchen.php");
            else header("Location: waiter_dashboard.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Restaurant Login</title>
<style>
body{font-family:Arial;background:#f5f5f5;text-align:center;}
form{background:#fff;padding:30px;margin:100px auto;width:300px;border-radius:8px;}
input{width:90%;padding:10px;margin:10px 0;}
button{padding:10px 20px;background:#007bff;color:#fff;border:none;cursor:pointer;}
</style>
</head>
<body>

<form method="POST">
<h2>Restaurant Login</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>

</body>
</html>
