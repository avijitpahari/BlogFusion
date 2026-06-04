<?php
/**
 * actions/admin_toggle_user.php
 * AJAX: Toggle is_active status for a user. Admin only.
 */
include "../include/session.php";
requireAdmin();
include "../include/db.php";

header('Content-Type: application/json');

$user_id = (int)($_POST['user_id'] ?? 0);
$status  = (int)($_POST['status']  ?? 0); // 1 = activate, 0 = deactivate

if ($user_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}

// Prevent admin from deactivating themselves
if ($user_id === (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'You cannot deactivate your own account.']);
    exit;
}

$new_status = $status ? 1 : 0;
$upd = mysqli_query($conn, "UPDATE users SET is_active = $new_status WHERE id = $user_id");

if ($upd) {
    echo json_encode(['success' => true, 'is_active' => $new_status]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
exit;
