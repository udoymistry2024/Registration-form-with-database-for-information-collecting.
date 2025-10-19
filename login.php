<?php
session_start();
include 'db.php';

// চেক করুন কোনো অ্যাডমিন আছে কিনা
$result = $conn->query("SELECT id FROM admin_users LIMIT 1");
if ($result->num_rows === 0) {
    header('Location: signup.php'); // কোনো অ্যাডমিন না থাকলে সাইন-আপ পেজে পাঠান
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password, role FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_result = $stmt->get_result();
    if ($user_result->num_rows == 1) {
        $user = $user_result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_role'] = $user['role'];
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    $error = "Invalid username or password!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        h2 { margin-top:0; margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .error { color: red; margin-top: 10px; font-size: 14px; }
        .links { margin-top: 15px; }
        .links a { color: #007bff; text-decoration: none; font-size: 14px; margin: 0 5px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        </form>
        <div class="links">
            <a href="forgot_password.php">Forgot Password?</a> | <a href="signup_invite.php">Sign Up with Invite Code</a>
        </div>
    </div>
</body>
</html>