<?php
include 'admin_layout.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("UPDATE settings SET setting_value = '$title' WHERE setting_name = 'form_title'");
}

$title_result = $conn->query("SELECT setting_value FROM settings WHERE setting_name = 'form_title'");
$current_title = ($title_result && $title_result->num_rows > 0) ? $title_result->fetch_assoc()['setting_value'] : '';

admin_header("Form Settings");
?>
<form method="post">
    <div style="margin-bottom: 15px;">
        <label for="title" style="display:block; margin-bottom:5px;">Form Title</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($current_title); ?>" style="width:100%; padding:10px; border-radius:4px; border:1px solid #ccc; box-sizing: border-box;">
    </div>
    <button type="submit" class="btn-primary">Save Settings</button>
</form>
<?php admin_footer(); ?>