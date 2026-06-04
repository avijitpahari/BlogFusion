<?php
// actions/toggle_post_status.php
// AJAX POST: toggle a post's status between published/draft
include '../include/session.php';
requireAuthor();
include '../include/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$post_id = isset($_POST['id'])     ? (int) $_POST['id']                          : 0;
$status  = isset($_POST['status']) ? $_POST['status']                            : '';
$user_id = $_SESSION['user_id'];

$allowed_statuses = ['published', 'draft'];
if (!$post_id || !in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Verify ownership
$check = mysqli_query($conn,
    "SELECT id FROM posts WHERE id = '$post_id' AND author_id = '$user_id' LIMIT 1"
);
if (!$check || mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (mysqli_query($conn, "UPDATE posts SET status = '$status' WHERE id = '$post_id'")) {
    if ($status === 'published') {
        $p_res = mysqli_query($conn, "SELECT title, author_id FROM posts WHERE id = '$post_id' LIMIT 1");
        $p_row = mysqli_fetch_assoc($p_res);
        if ($p_row) {
            $title = $p_row['title'];
            $author_id = $p_row['author_id'];
            $users_res = mysqli_query($conn, "SELECT id FROM users");
            while ($u_row = mysqli_fetch_assoc($users_res)) {
                $recipient_id = (int)$u_row['id'];
                if ($recipient_id != $author_id) {
                    add_notification($conn, $recipient_id, "New post published", "A new post has been published: '$title'", "post");
                }
            }
        }
    }
    echo json_encode(['success' => true, 'status' => $status]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}