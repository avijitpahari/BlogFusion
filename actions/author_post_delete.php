<?php
// actions/author_post_delete.php
// GET: delete a post owned by the logged-in author
include '../include/session.php';
requireAuthor();
include '../include/db.php';

$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

if (!$post_id) {
    header('Location: ../author/my-post.php?msg=invalid');
    exit;
}

// Verify ownership
$check = mysqli_query($conn,
    "SELECT id, image FROM posts WHERE id = '$post_id' AND author_id = '$user_id' LIMIT 1"
);
if (!$check || mysqli_num_rows($check) === 0) {
    header('Location: ../author/my-post.php?msg=not_found');
    exit;
}

$post = mysqli_fetch_assoc($check);

// Delete related comments (replies first, then top-level)
$comments = mysqli_query($conn, "SELECT id FROM comments WHERE post_id = '$post_id'");
while ($c = mysqli_fetch_assoc($comments)) {
    mysqli_query($conn, "DELETE FROM comments WHERE parent_id = '{$c['id']}'");
}
mysqli_query($conn, "DELETE FROM comments WHERE post_id = '$post_id'");

// Delete the post
$success = mysqli_query($conn, "DELETE FROM posts WHERE id = '$post_id' AND author_id = '$user_id'");
if ($success) {
    // Optionally remove blog image
    if (!empty($post['image']) && file_exists('../' . $post['image'])) {
        @unlink('../' . $post['image']);
    }
}

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete post.']);
    }
    exit;
}

if ($success) {
    header('Location: ../author/my-post.php?msg=deleted');
} else {
    header('Location: ../author/my-post.php?msg=delete_failed');
}
exit;