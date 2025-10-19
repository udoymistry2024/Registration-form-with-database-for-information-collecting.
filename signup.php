<?php
session_start();
include 'db.php';

// যদি ইতিমধ্যে কোনো অ্যাডমিন থাকে, তাহলে লগইন পেজে পাঠিয়ে দিন
$result = $conn->query("SELECT id FROM admin_users LIMIT 1");
if ($result->num_rows > 0) {
    header('Location: login.php');
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $plain_codes = [];
    for ($i = 0; $i < 5; $i++) {
        $plain_codes[] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }
    $security_codes_json = json_encode($plain_codes);

    $stmt = $conn->prepare("INSERT INTO admin_users (username, password, role, security_codes) VALUES (?, ?, 'owner', ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $security_codes_json);
    
    if ($stmt->execute()) {
        $_SESSION['security_codes'] = $plain_codes;
        header('Location: signup_success.php');
        exit();
    } else {
        $error = "Username already exists or database error!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Sign Up</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 360px; text-align: center; }
        h2 { margin-top:0; margin-bottom: 10px; color: #333; }
        p { color: #666; font-size: 14px; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .error { color: red; margin-top: 10px; font-size: 14px; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Create Owner Account</h2>
    <p>This is a one-time setup for the first administrator (owner).</p>
    <form method="post">
        <input type="text" name="username" placeholder="Choose a Username" required>
        <input type="password" name="password" placeholder="Choose a Password" required>
        <button type="submit">Create Account</button>
        <?php if($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    </form>
</div>
</body>
</html>