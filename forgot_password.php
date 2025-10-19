<?php
include 'db.php';
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $security_code = $_POST['security_code'];
    $new_password = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT security_codes FROM admin_users WHERE username = ? AND role = 'owner'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $saved_codes = json_decode($user['security_codes'], true);
        
        if (in_array($security_code, $saved_codes)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $update_stmt->bind_param("ss", $new_hashed_password, $username);
            $update_stmt->execute();
            $message = "Password has been reset successfully!";
            $message_type = "success";
        } else {
            $message = "Invalid username or security code.";
            $message_type = "error";
        }
    } else {
        $message = "Invalid username or you are not the owner.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        h2 { margin-top:0; margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .success { color: green; }
        .error { color: red; }
        a { color: #007bff; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Reset Password</h2>
    <?php if ($message): ?>
        <p class="<?php echo $message_type; ?>"><?php echo $message; ?></p>
        <a href="login.php">Back to Login</a>
    <?php else: ?>
        <form method="post">
            <input type="text" name="username" placeholder="Your Username" required>
            <input type="text" name="security_code" placeholder="One of your Security Codes" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>