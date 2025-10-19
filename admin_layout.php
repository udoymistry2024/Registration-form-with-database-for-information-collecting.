<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

function admin_header($title) {
    // Determine which link is active based on the current page
    $current_page = basename($_SERVER['PHP_SELF']);
    $active_dashboard = ($current_page == 'admin_dashboard.php') ? 'active' : '';
    $active_fields = ($current_page == 'manage_fields.php') ? 'active' : '';
    $active_settings = ($current_page == 'settings.php') ? 'active' : '';
    $active_users = ($current_page == 'manage_users.php') ? 'active' : '';

    // Start echoing the HTML content
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . ' - Admin Panel</title>
    <style>
        body { margin: 0; font-family: \'Segoe UI\', sans-serif; background-color: #f4f7fc; }
        .admin-wrapper { display: flex; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; min-height: 100vh; }
        .sidebar h2 { text-align: center; padding: 20px 0; background-color: #1a242f; margin: 0; font-size: 20px; }
        .sidebar ul { list-style-type: none; padding: 0; margin: 0; }
        .sidebar ul li a { display: block; padding: 15px 20px; color: #ecf0f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.3s ease; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #34495e; border-left-color: #3498db; }
        .main-content { flex-grow: 1; padding: 30px; }
        .content-box { background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1, h2 { color: #343a40; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f9f9f9; }
        button, .btn-primary, .btn-danger { padding: 10px 15px; border-radius: 5px; border: none; cursor: pointer; color: white; text-decoration: none; display: inline-block;}
        .btn-primary { background: #3498db; }
        .btn-danger { background: #e74c3c; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php" class="' . $active_dashboard . '">Dashboard</a></li>
            <li><a href="manage_fields.php" class="' . $active_fields . '">Manage Fields</a></li>
            <li><a href="settings.php" class="' . $active_settings . '">Settings</a></li>';
    
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'owner') {
        echo '<li><a href="manage_users.php" class="' . $active_users . '">Manage Users</a></li>';
    }

    echo '</ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>' . htmlspecialchars($title) . '</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="content-box">';
}

function admin_footer() {
    echo '        </div>
    </div>
</div>
</body>
</html>';
}
?>