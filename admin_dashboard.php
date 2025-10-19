<?php
include 'admin_layout.php';
include 'db.php';

// --- নতুন ডিলিট যুক্তি শুরু ---

// নির্দিষ্ট একটি সাবমিশন ডিলিট করার জন্য
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // submissions টেবিল থেকে ডিলিট করা হবে, user_data টেবিল থেকে ON DELETE CASCADE থাকায় স্বয়ংক্রিয়ভাবে মুছে যাবে
    $stmt = $conn->prepare("DELETE FROM submissions WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        // ডিলিট সফল হলে ড্যাশবোর্ডে রিডাইরেক্ট করুন
        header("Location: admin_dashboard.php?status=deleted");
        exit();
    } else {
        header("Location: admin_dashboard.php?status=error");
        exit();
    }
}

// সব সাবমিশন ডিলিট করার জন্য
if (isset($_GET['delete_all']) && $_GET['delete_all'] == 'true') {
    // TRUNCATE TABLE ব্যবহার করা হয়েছে কারণ এটি দ্রুত এবং সব ডেটা রিসেট করে দেয়
    if ($conn->query("TRUNCATE TABLE user_data") && $conn->query("TRUNCATE TABLE submissions")) {
        header("Location: admin_dashboard.php?status=all_deleted");
        exit();
    } else {
        header("Location: admin_dashboard.php?status=error");
        exit();
    }
}

// --- নতুন ডিলিট যুক্তি শেষ ---


// সব ফিল্ডের তালিকা নেওয়া
$fields_query = $conn->query("SELECT id, field_label, field_name FROM form_fields ORDER BY display_order ASC");
$fields = [];
while ($row = $fields_query->fetch_assoc()) {
    $fields[$row['id']] = [
        'label' => $row['field_label'],
        'name' => $row['field_name']
    ];
}

// সব সাবমিশনের তথ্য একসাথে আনা
$submissions_query = $conn->query("
    SELECT s.id, s.submission_date, ud.field_id, ud.field_value 
    FROM submissions s 
    JOIN user_data ud ON s.id = ud.submission_id 
    ORDER BY s.id DESC
");
$submissions = [];
$submission_ids = [];
while ($row = $submissions_query->fetch_assoc()) {
    if (!in_array($row['id'], $submission_ids)) {
        $submission_ids[] = $row['id'];
    }
    $submissions[$row['id']]['submission_date'] = $row['submission_date'];
    $submissions[$row['id']]['data'][$row['field_id']] = $row['field_value'];
}

admin_header("Dashboard");
?>
<!-- ডিলিট করার পর বার্তা দেখানোর জন্য -->
<?php if(isset($_GET['status'])): ?>
    <div style="padding: 10px; margin-bottom: 20px; border-radius: 5px; color: #155724; background-color: #d4edda;">
        <?php 
            if ($_GET['status'] == 'deleted') echo 'Submission deleted successfully.';
            if ($_GET['status'] == 'all_deleted') echo 'All submissions have been deleted successfully.';
        ?>
    </div>
<?php endif; ?>


<!-- Delete All বাটন -->
<div style="margin-bottom: 20px; text-align: right;">
    <?php if(!empty($submissions)): ?>
    <a href="admin_dashboard.php?delete_all=true" class="btn-danger" 
       onclick="return confirm('Are you sure you want to delete ALL submissions? This action cannot be undone.');">
       Delete All Submissions
    </a>
    <?php endif; ?>
</div>

<div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>Submission ID</th>
                <th>Date</th>
                <?php foreach ($fields as $field_data): ?>
                    <th><?php echo htmlspecialchars($field_data['label']); ?></th>
                <?php endforeach; ?>
                <th>Action</th> <!-- নতুন Action কলাম -->
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)): ?>
                <tr><td colspan="<?php echo count($fields) + 3; ?>">No data found.</td></tr>
            <?php else: ?>
                <?php foreach ($submission_ids as $id): 
                    $submission = $submissions[$id]; ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo date("d M, Y", strtotime($submission['submission_date'])); ?></td>
                        <?php foreach ($fields as $field_id => $field_data): ?>
                            <td>
                                <?php
                                $value = isset($submission['data'][$field_id]) ? htmlspecialchars($submission['data'][$field_id]) : '';
                                $field_type_query = $conn->query("SELECT field_type FROM form_fields WHERE id = $field_id");
                                
                                if ($field_type_query && $field_type_row = $field_type_query->fetch_assoc()) {
                                    if ($field_type_row['field_type'] == 'file' && !empty($value)) {
                                        $image_path = 'uploads/' . $value;
                                        echo '<div>';
                                        echo '<a href="' . $image_path . '" target="_blank">';
                                        echo '<img src="' . $image_path . '" alt="image" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border: 2px solid #ddd;">';
                                        echo '</a>';
                                        echo '<a href="' . $image_path . '" download="' . $value . '" style="display:block; margin-top:8px; font-size:12px; text-decoration:none;">Download</a>';
                                        echo '</div>';
                                    } else {
                                        echo $value;
                                    }
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                        <td> <!-- প্রতিটি সারির জন্য Delete বাটন -->
                            <a href="admin_dashboard.php?delete_id=<?php echo $id; ?>" 
                               class="btn-danger" 
                               style="padding: 5px 10px; font-size: 12px;" 
                               onclick="return confirm('Are you sure you want to delete this submission?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>