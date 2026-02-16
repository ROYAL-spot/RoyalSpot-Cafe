<?php
session_start();
require 'db_connect.php';

if(isset($_POST['login'])){
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($_POST['password']);

    $result = $conn->query("SELECT * FROM users 
                            WHERE username='$username' 
                            AND password='$password'");

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin'){
            header("Location: analytics_dashboard.php");
        } elseif($user['role'] == 'kitchen'){
            header("Location: kitchen.php");
        } else {
            header("Location: waiter_dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid credentials";
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
button{padding:10px 20px;background:#007bff;color:#fff;border:none;}
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
