<?php
include 'admin_layout.php';
include 'db.php';

if ($_SESSION['admin_role'] !== 'owner') {
    die("Access Denied! Only the owner can manage users.");
}

if (isset($_POST['generate_code'])) {
    $new_code = bin2hex(random_bytes(8)); // একটি র্যান্ডম কোড তৈরি
    $conn->query("INSERT INTO invitation_codes (code) VALUES ('$new_code')");
    header("Location: manage_users.php");
    exit();
}

$codes_result = $conn->query("SELECT code, is_used, created_at FROM invitation_codes ORDER BY id DESC");
$users_result = $conn->query("SELECT id, username, role FROM admin_users ORDER BY id ASC");

admin_header("Manage Users & Invites");
?>

<div style="margin-bottom: 40px;">
    <h2>Current Admin Users</h2>
    <table>
        <thead><tr><th>ID</th><th>Username</th><th>Role</th></tr></thead>
        <tbody>
            <?php while($row = $users_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo ucfirst($row['role']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div>
    <form method="post" style="margin-bottom: 20px;">
        <button type="submit" name="generate_code" class="btn-primary">Generate New Invite Code</button>
    </form>
    <h2>Invitation Codes</h2>
    <table>
        <thead><tr><th>Code</th><th>Status</th><th>Created At</th></tr></thead>
        <tbody>
            <?php if($codes_result->num_rows > 0): ?>
                <?php while($row = $codes_result->fetch_assoc()): ?>
                    <tr>
                        <td><code><?php echo $row['code']; ?></code></td>
                        <td><?php echo $row['is_used'] ? '<span style="color:red;">Used</span>' : '<span style="color:green;">Available</span>'; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No invitation codes generated yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php admin_footer(); ?>