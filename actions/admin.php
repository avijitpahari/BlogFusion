<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin(); // Enforce strict administrative privilege checks
include BASE_PATH . 'include/db.php';
global $conn;
include BASE_PATH . 'include/data_fetch.php';

// =========  user update section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user-update'])) {
    $user_id = (int)$_POST['user_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $role = mysqli_real_escape_string($conn, trim($_POST['role'] ?? 'user'));
    
    // Retrieve current profile image from database to prevent path traversal file deletion
    $user_res = mysqli_query($conn, "SELECT profile_image FROM users WHERE id=$user_id LIMIT 1");
    $current_profile_image = 'upload/profile-images/default.png';
    if ($user_res && mysqli_num_rows($user_res) > 0) {
        $current_profile_image = mysqli_fetch_assoc($user_res)['profile_image'];
    }

    $path = $current_profile_image;
    $file = $_FILES['new_image'] ?? null;
    $upload_ok = false;

    if ($file && !empty($file['tmp_name'])) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                // Secure unique filename creation
                $filename = 'admin_user_' . uniqid('img_', true) . '.' . $ext;
                $path = 'upload/profile-images/' . $filename;
                $upload_ok = true;
            }
        }
    }

    $sql = "UPDATE users SET name='$name', email='$email', role='$role', profile_image='$path' WHERE id=$user_id";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if ($upload_ok) {
            move_uploaded_file($file['tmp_name'], BASE_PATH . $path);
            if ($current_profile_image && $current_profile_image !== 'upload/profile-images/default.png' && file_exists(BASE_PATH . $current_profile_image)) {
                @unlink(BASE_PATH . $current_profile_image);
            }
        }
        if ((string)$_SESSION['user_id'] === (string)$user_id) {
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
        }
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        header("Location: " . BASE_URL . "admin/users.php?page=" . $page);
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/users.php?msg=update_fail");
        exit();
    }
}


// =========  user create section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_user'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, trim($_POST['role'] ?? 'user'));
    
    $path = 'upload/profile-images/default.png';
    $file = $_FILES['new_image'] ?? null; // Fixed key mismatch
    $upload_ok = false;

    if ($file && !empty($file['tmp_name'])) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                $filename = 'admin_user_' . uniqid('img_', true) . '.' . $ext;
                $path = 'upload/profile-images/' . $filename;
                $upload_ok = true;
            }
        }
    }

    $query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        header("Location: " . BASE_URL . "admin/users.php?msg=email");
        exit();
    } else {
        $sql = "INSERT INTO users (name, email, password, role, profile_image) VALUES ('$name', '$email', '$password', '$role', '$path')";
        if (mysqli_query($conn, $sql)) {
            if ($upload_ok) {
                move_uploaded_file($file['tmp_name'], BASE_PATH . $path);
            }
        } else {
            header("Location: " . BASE_URL . "admin/users.php?msg=user_add_fail");
            exit();
        }
    }
}


// =========  user delete section  ==========

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'user') {
    $id = (int)$_GET['id'];
    
    // Prevent admin deleting themselves
    if ((int)$_SESSION['user_id'] === $id) {
        header("Location: " . BASE_URL . "admin/users.php?msg=delete_self_error");
        exit();
    }
    
    $call = delete('users', $id);
    if ($call) {
        header("Location: " . BASE_URL . "admin/users.php?msg=user_deleted");
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/users.php?msg=user_delete_fail");
        exit();
    }
}

// =========  category update section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category-edit'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $slug = mysqli_real_escape_string($conn, trim($_POST['slug'] ?? ''));
    
    $sql = "UPDATE categories SET name='$name', slug='$slug' WHERE id=$id";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        header("Location: " . BASE_URL . "admin/categories.php?page=" . $page);
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/categories.php?msg=update_fail");
        exit();
    }
}


// =========  category add section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category-add'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $slug = mysqli_real_escape_string($conn, trim($_POST['slug'] ?? ''));
    
    $query = "SELECT id FROM categories WHERE slug='$slug' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        header("Location: " . BASE_URL . "admin/categories.php?page=" . $page);
        exit();
    } else {
        $sql = "INSERT INTO categories (name,slug) VALUES ('$name','$slug')";
        if (mysqli_query($conn, $sql)) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            header("Location: " . BASE_URL . "admin/categories.php?page=" . $page);
            exit();
        }
    }
}

// =========  category approve section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_status_update'])) {
    $request_id = (int)$_POST['request_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

    $sql = "UPDATE category_requests SET status='$status' WHERE id=$request_id";
    $run = mysqli_query($conn, $sql);
    
    if ($status == 'approved') {
        $query = "SELECT * FROM category_requests WHERE id = $request_id LIMIT 1";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $request_data = mysqli_fetch_assoc($result);
            $request_data_name = mysqli_real_escape_string($conn, $request_data['category_name']);
            $request_data_slug = mysqli_real_escape_string($conn, $request_data['category_slug']);
            
            $check_query = "SELECT id FROM categories WHERE slug='$request_data_slug' LIMIT 1";
            $check_result = mysqli_query($conn, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) == 0) {
                $query = "INSERT INTO categories (name,slug) VALUES ('$request_data_name','$request_data_slug')";
                mysqli_query($conn, $query);
            }
        }
    }
    header("Location: " . BASE_URL . "admin/categories.php");
    exit();
}

// =========  category delete section  ==========

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'category') {
    $id = (int)$_GET['id'];
    $call = delete('categories', $id);
    if ($call) {
        header("Location: " . BASE_URL . "admin/categories.php?msg=category_deleted");
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/categories.php?msg=category_delete_fail");
        exit();
    }
}

// =========  post delete section  ==========

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'post') {
    $id = (int)$_GET['id'];
    
    // Retrieve post image to delete it from filesystem
    $post_res = mysqli_query($conn, "SELECT image FROM posts WHERE id=$id LIMIT 1");
    if ($post_res && mysqli_num_rows($post_res) > 0) {
        $post_row = mysqli_fetch_assoc($post_res);
        $post_image = $post_row['image'];
        if ($post_image && file_exists(BASE_PATH . $post_image)) {
            @unlink(BASE_PATH . $post_image);
        }
    }
    
    // Delete comments & reactions first
    mysqli_query($conn, "DELETE FROM comments WHERE post_id=$id");
    mysqli_query($conn, "DELETE FROM reactions WHERE post_id=$id");
    
    $call = delete('posts', $id);
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        if ($call) {
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post.']);
        }
        exit();
    }
    if ($call) {
        header("Location: " . BASE_URL . "admin/posts.php?msg=post_deleted");
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/posts.php?msg=post_delete_fail");
        exit();
    }
}

// =========  comment delete section  ==========

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'comment') {
    $id = (int)$_GET['id'];
    // Delete replies first
    mysqli_query($conn, "DELETE FROM comments WHERE parent_id = $id");
    // Delete comment
    $call = delete('comments', $id);
    if ($call) {
        header("Location: " . BASE_URL . "admin/comments.php?msg=comment_deleted");
        exit();
    } else {
        header("Location: " . BASE_URL . "admin/comments.php?msg=comment_delete_fail");
        exit();
    }
}
?>