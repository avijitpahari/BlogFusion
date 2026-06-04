<?php
session_start();
include "../include/db.php";
global $conn;

// =========  signup section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $email = trim($_POST['email'] ?? '');
    
    // Check if email OTP verification was completed for this email
    if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true || ($_SESSION['verified_email'] ?? '') !== $email) {
        header("Location: ../pages/register.php?msg=verification_failed");
        exit();
    }

    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, $email);
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    
    // Restrict registration roles to 'user' or 'author' to prevent privilege escalation
    $role = in_array($_POST['role'] ?? 'user', ['user', 'author']) ? $_POST['role'] : 'user';

    $path = 'upload/profile-images/default.png';
    $file = $_FILES['image'] ?? null;
    $upload_ok = false;

    if ($file && !empty($file['tmp_name'])) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            // Secure MIME type verification
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                // Generate secure random unique filename to prevent path traversal and execution
                $filename = 'user_' . uniqid('img_', true) . '.' . $ext;
                $path = 'upload/profile-images/' . $filename;
                $upload_ok = true;
            }
        }
    }

    $query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        header("Location: ../pages/register.php?msg=exists");
        exit();
    } else {
        $sql = "INSERT INTO users (name, email, password, role, profile_image) VALUES ('$name', '$email', '$password', '$role', '$path')";
        if (mysqli_query($conn, $sql)) {
            if ($upload_ok) {
                move_uploaded_file($file['tmp_name'], '../' . $path);
            }
            // Clear verification markers from session
            unset($_SESSION['verified']);
            unset($_SESSION['verified_email']);
            header("Location: ../pages/login.php?msg=registered");
            exit();
        }
    }
}


// =========  login section  =========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    
    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        if (password_verify($password, $data['password'])) {
            // Check if account is active
            if (isset($data['is_active']) && (int)$data['is_active'] === 0) {
                header("Location: ../pages/login.php?msg=inactive");
                exit;
            }
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true); // Protect against Session Fixation
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['name'] = $data['name'];
            $_SESSION['msg'] = 'login_success';
            
            if ($data['role'] == "user") {
                header("Location: ../pages/home.php");
            } elseif ($data['role'] == "author") {
                header("Location: ../author/index.php");
            } elseif ($data['role'] == "admin") {
                header("Location: ../admin/dashboard.php");
            }
            exit;
        } else {
            header("Location: ../pages/login.php?msg=p_not_match");
            exit;
        }
    } else {
        header("Location: ../pages/login.php?msg=u_not_find");
        exit;
    }
}
?>