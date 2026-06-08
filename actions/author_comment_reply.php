<?php
// actions/author_comment_reply.php
// Handles AJAX POST: author replies to a comment
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$parent_id = isset($_POST['parent_id']) ? (int) $_POST['parent_id'] : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$user_id = $_SESSION['user_id'];

if (!$parent_id || !$comment) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// Verify the parent comment belongs to a post authored by this user
$check = mysqli_query(
    $conn,
    "SELECT c.id FROM comments c
     INNER JOIN posts p ON c.post_id = p.id
     WHERE c.id = '$parent_id' AND p.author_id = '$user_id'
     LIMIT 1"
);
if (!$check || mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get post_id from parent comment
$parent_row = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT post_id FROM comments WHERE id = '$parent_id' LIMIT 1"
));
$post_id = (int) ($parent_row['post_id'] ?? 0);

$comment_escaped = mysqli_real_escape_string($conn, $comment);
$sql = "INSERT INTO comments (post_id, user_id, comment, parent_id, created_at)
        VALUES ('$post_id', '$user_id', '$comment_escaped', '$parent_id', NOW())";

if (mysqli_query($conn, $sql)) {
    $new_id = mysqli_insert_id($conn);

    // Fetch author profile for response
    $user_row = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT name, profile_image FROM users WHERE id = '$user_id' LIMIT 1"
    ));

    echo json_encode([
        'success' => true,
        'reply' => [
            'id' => $new_id,
            'comment' => $comment,
            'parent_id' => $parent_id,
            'user_id' => $user_id,
            'name' => htmlspecialchars($user_row['name'] ?? 'You'),
            'profile_image' => BASE_URL . ($user_row['profile_image'] ?? 'upload/profile-images/default.png'),
            'created_at' => date('M d, Y · H:i'),
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}