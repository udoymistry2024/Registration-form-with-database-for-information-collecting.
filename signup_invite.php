<?php
include 'db.php';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invite_code = $_POST['invite_code'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id FROM invitation_codes WHERE code = ? AND is_used = 0");
    $stmt->bind_param("s", $invite_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insert_stmt = $conn->prepare("INSERT INTO admin_users (username, password, role) VALUES (?, ?, 'admin')");
        $insert_stmt->bind_param("ss", $username, $hashed_password);
        if($insert_stmt->execute()){
            $conn->query("UPDATE invitation_codes SET is_used = 1 WHERE code = '$invite_code'");
            header("Location: login.php");
            exit();
        } else {
            $error = "Username already exists!";
        }
    } else {
        $error = "Invalid or already used invitation code!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up with Invite Code</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        h2 { margin-top:0; margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .error { color: red; margin-top: 10px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Create Admin Account</h2>
        <form method="post">
            <input type="text" name="invite_code" placeholder="Invitation Code" required>
            <input type="text" name="username" placeholder="Choose a Username" required>
            <input type="password" name="password" placeholder="Choose a Password" required>
            <button type="submit">Sign Up</button>
            <?php if($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        </form>
    </div>
</body>
</html>