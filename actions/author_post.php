<?php
include '../include/session.php';
requireAuthor();
include '../include/db.php';
include '../include/functions.php';
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $raw_slug = trim($_POST['slug'] ?? '');
    $slug = mysqli_real_escape_string($conn, get_unique_slug($conn, $_POST['title'] ?? '', $raw_slug));
    $description = mysqli_real_escape_string($conn, trim($_POST['short_description'] ?? ''));
    $content = mysqli_real_escape_string($conn, trim($_POST['content'] ?? ''));
    $category_id = (int)($_POST['category_id'] ?? 0);
    $status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'draft'));
    $author_id = (int)$_SESSION['user_id'];
    $tags = $_POST['tags'] ?? [];
    $tags_json = mysqli_real_escape_string($conn, json_encode($tags));

    $path = "";
    $file = $_FILES['blog_image'] ?? null;
    $upload_ok = false;

    if ($file && !empty($file['tmp_name'])) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                // Secure unique filename to prevent path traversal and execution
                $filename = 'post_' . uniqid('img_', true) . '.' . $ext;
                $path = 'upload/blog_image/' . $filename;
                $upload_ok = true;
            }
        }
    }

    $query = "INSERT INTO posts (title, slug, description, content, image, category_id, author_id, tag, status) VALUES ('$title', '$slug', '$description', '$content', '$path', $category_id, $author_id, ' $tags_json', '$status')";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if ($upload_ok) {
            move_uploaded_file($file['tmp_name'], '../' . $path);
        }
        
        // Notify all users if post is published
        if ($status === 'published') {
            $users_res = mysqli_query($conn, "SELECT id FROM users");
            if ($users_res) {
                while ($u_row = mysqli_fetch_assoc($users_res)) {
                    $recipient_id = (int)$u_row['id'];
                    if ($recipient_id != $author_id) {
                        add_notification($conn, $recipient_id, "New post published", "A new post has been published: '$title'", "post");
                    }
                }
            }
        }
        
        header("Location: ../author/my-post.php?msg=posted");
        exit();
    } else {
        header("Location: ../author/edit-post.php?msg=post_fail");
        exit();
    }
}
?>