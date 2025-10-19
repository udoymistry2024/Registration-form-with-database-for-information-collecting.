<?php
session_start();
if (!isset($_SESSION['security_codes'])) {
    header('Location: login.php');
    exit();
}
$codes = $_SESSION['security_codes'];
unset($_SESSION['security_codes']); // একবার দেখানোর পর সেশন থেকে মুছে দিন
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up Successful</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 400px; text-align: left; }
        h2 { margin-top:0; color: #28a745; text-align: center; }
        p { color: #e74c3c; font-weight: bold; font-size: 15px; line-height: 1.5; border: 1px solid #e74c3c; padding: 10px; border-radius: 5px; }
        ul { list-style-type: none; padding: 0; margin: 20px 0; }
        li { background-color: #ecf0f1; border: 1px solid #bdc3c7; padding: 10px; margin-bottom: 10px; border-radius: 5px; font-size: 18px; text-align: center; letter-spacing: 2px; }
        a.button { display: block; text-align: center; background: #007bff; color: white; padding: 12px; border-radius: 5px; text-decoration: none; font-size: 16px; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Account Created Successfully!</h2>
    <p>IMPORTANT: Please save these security codes. You will need them to reset your password. You will not see them again.</p>
    <ul>
        <?php foreach ($codes as $code): ?>
            <li><strong><?php echo $code; ?></strong></li>
        <?php endforeach; ?>
    </ul>
    <a href="login.php" class="button">Proceed to Login</a>
</div>
</body>
</html>