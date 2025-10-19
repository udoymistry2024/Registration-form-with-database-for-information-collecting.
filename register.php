<?php
include 'db.php';
session_start();

$message = '';
$message_type = 'error';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fields'])) {
    
    // --- ডেটা রিপ্লেসমেন্টের জন্য নতুন যুক্তি ---

    // ধাপ ১: ব্যবহারকারীকে চেনার জন্য মূল ফিল্ডগুলো শনাক্ত করা (যেমন: রোল এবং ইমেল)
    $identifier_fields = ['roll_number', 'email_address'];
    $identifier_values = [];

    // ডেটাবেস থেকে এই 필্ডগুলোর ID খুঁজে বের করা
    $field_ids_query = "SELECT id, field_name FROM form_fields WHERE field_name IN ('" . implode("','", $identifier_fields) . "')";
    $field_ids_result = $conn->query($field_ids_query);
    $field_map = [];
    while($row = $field_ids_result->fetch_assoc()) {
        $field_map[$row['field_name']] = $row['id'];
    }

    // পোস্ট করা ডেটা থেকে শনাক্তকারী মানগুলো সংগ্রহ করা
    foreach ($identifier_fields as $field_name) {
        if (isset($field_map[$field_name]) && isset($_POST['fields'][$field_map[$field_name]])) {
            $identifier_values[$field_map[$field_name]] = $_POST['fields'][$field_map[$field_name]];
        }
    }

    // ধাপ ২: এই শনাক্তকারী মান দিয়ে পুরনো কোনো সাবমিশন আছে কিনা তা খোঁজা
    if (count($identifier_values) == count($identifier_fields)) {
        $sub_query = "SELECT submission_id FROM user_data WHERE ";
        $conditions = [];
        foreach ($identifier_values as $field_id => $value) {
            $conditions[] = "(field_id = " . intval($field_id) . " AND field_value = '" . $conn->real_escape_string($value) . "')";
        }
        $sub_query .= implode(' AND submission_id IN (SELECT submission_id FROM user_data WHERE ', $conditions);
        for ($i=0; $i < count($conditions) - 1; $i++) { $sub_query .= ')'; }
        
        $existing_submission_result = $conn->query($sub_query);

        // ধাপ ৩: যদি পুরনো সাবমিশন পাওয়া যায়, তবে সেটি মুছে ফেলা
        if ($existing_submission_result && $existing_submission_result->num_rows > 0) {
            $old_submission_id = $existing_submission_result->fetch_assoc()['submission_id'];
            $conn->query("DELETE FROM submissions WHERE id = " . intval($old_submission_id));
            // user_data টেবিলের ডেটা ON DELETE CASCADE থাকায় স্বয়ংক্রিয়ভাবে মুছে যাবে
        }
    }
    
    // --- নতুন ডেটা যোগ করার প্রক্রিয়া ---

    $conn->begin_transaction();
    try {
        // submissions টেবিলে একটি নতুন row তৈরি করে submission ID নেওয়া
        $stmt_sub = $conn->prepare("INSERT INTO submissions (submission_date) VALUES (NOW())");
        $stmt_sub->execute();
        $submission_id = $stmt_sub->insert_id;
        $stmt_sub->close();

        // user_data টেবিলে তথ্য সংরক্ষণের জন্য statement
        $stmt_data = $conn->prepare("INSERT INTO user_data (submission_id, field_id, field_value) VALUES (?, ?, ?)");
        
        // প্রতিটি ফিল্ডের তথ্য ডেটাবেসে সংরক্ষণ করা
        foreach ($_POST['fields'] as $field_id => $value) {
            $field_value = is_array($value) ? implode(', ', $value) : $value;
            if (!empty($field_value)) { // শুধুমাত্র খালি না থাকলে যোগ করুন
                $stmt_data->bind_param("iis", $submission_id, $field_id, $field_value);
                $stmt_data->execute();
            }
        }

        // ফাইল আপলোড হ্যান্ডেল করা
        if (isset($_FILES['fields'])) {
            foreach ($_FILES['fields']['name'] as $field_id => $name) {
                if ($_FILES['fields']['error'][$field_id] === UPLOAD_ERR_OK) {
                    $target_dir = "uploads/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $file_name = time() . '_' . uniqid() . '_' . basename($name);
                    $target_file = $target_dir . $file_name;
                    move_uploaded_file($_FILES['fields']['tmp_name'][$field_id], $target_file);
                    
                    $stmt_data->bind_param("iis", $submission_id, $field_id, $file_name);
                    $stmt_data->execute();
                }
            }
        }
        $stmt_data->close();
        $conn->commit();
        $message = "Registration successful!";
        $message_type = "success";

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "Error: " . $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Status</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; }
        .status-box { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        .success { border-left: 5px solid #28a745; }
        .success h2 { color: #28a745; }
        .error { border-left: 5px solid #dc3545; }
        .error h2 { color: #dc3545; }
        a { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="status-box <?php echo $message_type; ?>">
        <h2><?php echo $message; ?></h2>
        <a href="index.php">Go Back to Form</a>
    </div>
</body>
</html>