<?php
include "../include/session.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}
include "../include/db.php";
global $conn;

// =========  user/author profile update via home.php  ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $user_id = (int)$_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    
    // Check if email already taken
    $email_check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $user_id LIMIT 1");
    if ($email_check && mysqli_num_rows($email_check) > 0) {
        header("Location: ../pages/home.php?msg=email");
        exit();
    }
    
    // Retrieve actual current profile image from database
    $user_res = mysqli_query($conn, "SELECT profile_image FROM users WHERE id=$user_id LIMIT 1");
    $current_profile_image = 'upload/profile-images/default.png';
    if ($user_res && mysqli_num_rows($user_res) > 0) {
        $current_profile_image = mysqli_fetch_assoc($user_res)['profile_image'];
    }
    
    $path = $current_profile_image;
    $file = $_FILES['profile_image'] ?? null;
    $upload_ok = false;

    if ($file && !empty($file['tmp_name'])) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                $filename = 'user_' . uniqid('img_', true) . '.' . $ext;
                $path = 'upload/profile-images/' . $filename;
                $upload_ok = true;
            }
        }
    }
    
    $sql = "UPDATE users SET name='$name', email='$email', profile_image='$path' WHERE id=$user_id";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if ($upload_ok) {
            move_uploaded_file($file['tmp_name'], '../' . $path);
            if ($current_profile_image && $current_profile_image !== 'upload/profile-images/default.png' && file_exists('../' . $current_profile_image)) {
                @unlink('../' . $current_profile_image);
            }
        }
        $_SESSION['name'] = $name;
        if (isset($_SESSION['user_name'])) {
            $_SESSION['user_name'] = $name;
        }
        header("Location: ../pages/home.php?msg=profile_updated");
        exit();
    } else {
        header("Location: ../pages/home.php?msg=update_fail");
        exit();
    }
    exit;
}

// =========  author profile update section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profile_update'])) {
    requireAuthor(); // Must be author to use author panel updates
    $session_user_id = (int)$_SESSION['user_id'];
    $user_id = (int)$_POST['id'];
    if ($user_id !== $session_user_id) {
        header("Location: ../author/profile.php?msg=unauthorized");
        exit();
    }
    
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    
    // Retrieve actual current profile image from database
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
                $filename = 'author_' . uniqid('img_', true) . '.' . $ext;
                $path = 'upload/profile-images/' . $filename;
                $upload_ok = true;
            }
        }
    }
    
    $sql = "UPDATE users SET name='$name', profile_image='$path' WHERE id=$user_id";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if ($upload_ok) {
            move_uploaded_file($file['tmp_name'], '../' . $path);
            if ($current_profile_image && $current_profile_image !== 'upload/profile-images/default.png' && file_exists('../' . $current_profile_image)) {
                @unlink('../' . $current_profile_image);
            }
        }
        $_SESSION['name'] = $name;
        if (isset($_SESSION['user_name'])) {
            $_SESSION['user_name'] = $name;
        }
        header("Location: ../author/profile.php?msg=profile_updated");
        exit();
    } else {
        header("Location: ../author/profile.php?msg=update_fail");
        exit();
    }
}
?>
