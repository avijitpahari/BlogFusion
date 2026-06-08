<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $author_id = (int)$_SESSION['user_id'];

    // Admin can update ANY post — no author_id restriction
    $check_res = mysqli_query($conn, "SELECT id, image, status FROM posts WHERE id = $post_id LIMIT 1");
    if (!$check_res || mysqli_num_rows($check_res) === 0) {
        header("Location: " . BASE_URL . "admin/posts.php?error=Post+not+found");
        exit();
    }

    $post_row   = mysqli_fetch_assoc($check_res);
    $old_image  = $post_row['image'] ?? '';
    $old_status = $post_row['status'] ?? '';

    $title        = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $raw_slug     = trim($_POST['slug'] ?? '');
    $slug         = mysqli_real_escape_string($conn, get_unique_slug($conn, $_POST['title'] ?? '', $raw_slug, $post_id));
    $description  = mysqli_real_escape_string($conn, trim($_POST['short_description'] ?? ''));
    $content      = mysqli_real_escape_string($conn, trim($_POST['content'] ?? ''));
    $category_id  = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $status       = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'draft'));
    $tags         = $_POST['tags'] ?? [];
    $tags_json    = mysqli_real_escape_string($conn, json_encode($tags));
    $delete_image = isset($_POST['delete_image']) && $_POST['delete_image'] === '1';

    $path      = $old_image;
    $file      = $_FILES['blog_image'] ?? null;
    $upload_ok = false;

    if ($file && !empty($file['tmp_name'])) {
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                $filename  = 'post_' . uniqid('img_', true) . '.' . $ext;
                $path      = 'upload/blog_image/' . $filename;
                $upload_ok = true;
            }
        }
    } elseif ($delete_image) {
        $path = '';
    }

    $query  = "UPDATE posts SET title='$title', slug='$slug', description='$description', content='$content', image='$path', category_id=$category_id, tag='$tags_json', status='$status' WHERE id=$post_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if ($upload_ok) {
            move_uploaded_file($file['tmp_name'], BASE_PATH . $path);
            if ($old_image && file_exists(BASE_PATH . $old_image)) {
                @unlink(BASE_PATH . $old_image);
            }
        } elseif ($delete_image) {
            if ($old_image && file_exists(BASE_PATH . $old_image)) {
                @unlink(BASE_PATH . $old_image);
            }
        }

        // Notify all users if post is newly published
        if ($old_status !== 'published' && $status === 'published') {
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

        header("Location: " . BASE_URL . "admin/posts.php?msg=Post+updated+successfully");
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/edit-post.php?id=$post_id&error=Failed+to+update+post");
        exit();
    }
}
?>
