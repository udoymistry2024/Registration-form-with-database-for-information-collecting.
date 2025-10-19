<?php
include 'db.php';
// ডেটাবেস থেকে ফর্মের শিরোনাম এবং ফিল্ড আনা
$title_result = $conn->query("SELECT setting_value FROM settings WHERE setting_name = 'form_title'");
$form_title = ($title_result && $title_result->num_rows > 0) ? $title_result->fetch_assoc()['setting_value'] : 'Registration Form';
$fields_result = $conn->query("SELECT * FROM form_fields ORDER BY display_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($form_title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab); background-size: 400% 400%; animation: gradient 15s ease infinite; }
        @keyframes gradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .container { background-color: rgba(0, 0, 0, 0.2); padding: 40px; border-radius: 20px; box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5); width: 100%; max-width: 600px; backdrop-filter: blur(10px); }
        h2 { text-align: center; color: #fff; margin-bottom: 30px; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; color: rgba(255, 255, 255, 0.8); margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 14px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.1); color: #fff; font-size: 16px; box-sizing: border-box; border: 1px solid rgba(255, 255, 255, 0.3); }
        .form-group input::placeholder { color: rgba(255, 255, 255, 0.7) !important; opacity: 1; }
        .file-upload-wrapper input[type="file"] { display: none; }
        .file-upload-button { display: inline-flex; padding: 12px 18px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.2); color: #fff; cursor: pointer; }
        .file-name { margin-left: 15px; color: #fff; }
        .submit-btn { width: 100%; padding: 15px; border: none; border-radius: 8px; background: #fff; color: #e73c7e; font-size: 18px; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($form_title); ?></h2>
        <form action="register.php" method="post" enctype="multipart/form-data">
             <?php while($field = $fields_result->fetch_assoc()): ?>
                <div class="form-group">
                    <label for="field_<?php echo $field['id']; ?>"><?php echo htmlspecialchars($field['field_label']); ?></label>
                    <?php if ($field['field_type'] == 'file'): ?>
                        <div class="file-upload-wrapper">
                            <label for="field_<?php echo $field['id']; ?>" class="file-upload-button"><span>Browse File</span></label>
                            <input type="file" id="field_<?php echo $field['id']; ?>" name="fields[<?php echo $field['id']; ?>]" <?php if($field['is_required']) echo 'required'; ?>>
                            <span class="file-name" id="file-name-<?php echo $field['id']; ?>">No file chosen</span>
                        </div>
                    <?php else: ?>
                        <input type="<?php echo htmlspecialchars($field['field_type']); ?>" id="field_<?php echo $field['id']; ?>" name="fields[<?php echo $field['id']; ?>]" placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>" <?php if($field['is_required']) echo 'required'; ?>>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            <button type="submit" class="submit-btn"><span>Submit</span></button>
        </form>
    </div>
    <script>
    <?php 
    mysqli_data_seek($fields_result, 0); // Reset result pointer for the loop below
    while($field = $fields_result->fetch_assoc()): 
        if ($field['field_type'] == 'file'): ?>
        document.getElementById('field_<?php echo $field['id']; ?>').addEventListener('change', function() {
            const fileNameDisplay = document.getElementById('file-name-<?php echo $field['id']; ?>');
            fileNameDisplay.textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen';
        });
    <?php endif; endwhile; ?>
    </script>
</body>
</html>