<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Category name cannot be empty.']);
    exit();
}

$name_escaped = mysqli_real_escape_string($conn, $name);

// Check if already exists
$check = mysqli_query($conn, "SELECT id FROM categories WHERE name = '$name_escaped' LIMIT 1");
if ($check && mysqli_num_rows($check) > 0) {
    $row = mysqli_fetch_assoc($check);
    echo json_encode(['success' => false, 'message' => 'Category already exists.', 'id' => $row['id']]);
    exit();
}

$result = mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name_escaped')");
if ($result) {
    $new_id = mysqli_insert_id($conn);
    echo json_encode(['success' => true, 'id' => $new_id, 'name' => $name]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>
