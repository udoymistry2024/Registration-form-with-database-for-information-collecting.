<?php
include 'admin_layout.php';
include 'db.php';
// নতুন ফিল্ড যোগ করা
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_field'])) {
    $name = strtolower(str_replace(' ', '_', $_POST['label']));
    $stmt = $conn->prepare("INSERT INTO form_fields (field_name, field_label, field_type, is_required, placeholder, display_order) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisi", $name, $_POST['label'], $_POST['type'], $_POST['required'], $_POST['placeholder'], $_POST['order']);
    $stmt->execute();
    header("Location: manage_fields.php"); exit();
}
// ফিল্ড মুছে ফেলা
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM form_fields WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: manage_fields.php"); exit();
}
$fields_result = $conn->query("SELECT * FROM form_fields ORDER BY display_order ASC");

admin_header("Manage Form Fields");
?>
<h2>Current Fields</h2>
<table>
    <thead><tr><th>Order</th><th>Label</th><th>Type</th><th>Required</th><th>Action</th></tr></thead>
    <tbody>
        <?php while ($row = $fields_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['display_order']; ?></td>
                <td><?php echo htmlspecialchars($row['field_label']); ?></td>
                <td><?php echo $row['field_type']; ?></td>
                <td><?php echo $row['is_required'] ? 'Yes' : 'No'; ?></td>
                <td><a href="?delete=<?php echo $row['id']; ?>" class="btn-danger" style="padding: 5px 10px; color:white; text-decoration:none;" onclick="return confirm('Are you sure?')">Delete</a></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h2 style="margin-top: 40px;">Add New Field</h2>
<form method="post">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="form-group"><label>Label</label><input type="text" name="label" required style="width:100%; padding:8px;"></div>
        <div class="form-group"><label>Placeholder</label><input type="text" name="placeholder" style="width:100%; padding:8px;"></div>
        <div class="form-group"><label>Field Type</label><select name="type" style="width:100%; padding:8px;"><option value="text">Text</option><option value="email">Email</option><option value="tel">Telephone</option><option value="file">File Upload</option></select></div>
        <div class="form-group"><label>Is Required?</label><select name="required" style="width:100%; padding:8px;"><option value="1">Yes</option><option value="0">No</option></select></div>
        <div class="form-group"><label>Display Order</label><input type="number" name="order" value="100" style="width:100%; padding:8px;"></div>
    </div>
    <button type="submit" name="add_field" class="btn-primary" style="margin-top: 10px;">Add Field</button>
</form>
<?php admin_footer(); ?>