<?php
// actions/delete_comment.php
// AJAX POST: delete a comment (and cascade replies) for the logged-in author
include '../include/session.php';
requireAuthor();
include '../include/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$comment_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$user_id    = $_SESSION['user_id'];

if (!$comment_id) {
    echo json_encode(['success' => false, 'message' => 'Missing comment ID']);
    exit;
}

// Verify comment belongs to a post authored by this user
$check = mysqli_query($conn,
    "SELECT c.id FROM comments c
     INNER JOIN posts p ON c.post_id = p.id
     WHERE c.id = '$comment_id' AND p.author_id = '$user_id'
     LIMIT 1"
);
if (!$check || mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or not found']);
    exit;
}

// Delete replies first
mysqli_query($conn, "DELETE FROM comments WHERE parent_id = '$comment_id'");

// Delete comment
if (mysqli_query($conn, "DELETE FROM comments WHERE id = '$comment_id'")) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . mysqli_error($conn)]);
}