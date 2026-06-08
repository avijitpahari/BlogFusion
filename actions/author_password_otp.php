<?php
/**
 * actions/author_password_otp.php
 * Handles 3 actions via AJAX (POST + JSON response):
 *   1. send_otp       – verify old password, generate & email 6-digit OTP
 *   2. verify_otp     – check OTP, return a short-lived verified token stored in session
 *   3. change_password – verify token, update password
 */
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';

header('Content-Type: application/json');

$action  = $_POST['action'] ?? '';
$user_id = (int)$_SESSION['user_id'];

/* ── fetch user row ──────────────────────────────────────── */
$u_res = mysqli_query($conn, "SELECT name, email, password FROM users WHERE id = $user_id LIMIT 1");
$user  = mysqli_fetch_assoc($u_res);

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

/* ─────────────────────────────────────────────────────────
   ACTION 1 — send_otp
   Verify old password, generate OTP, send email
───────────────────────────────────────────────────────── */
if ($action === 'send_otp') {
    $old_pwd = $_POST['old_password'] ?? '';

    if (!password_verify($old_pwd, $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Incorrect current password']);
        exit;
    }

    // Generate 6-digit OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Store OTP + expiry in session (10 min)
    $_SESSION['pwd_otp']        = $otp;
    $_SESSION['pwd_otp_expire'] = time() + 600;
    $_SESSION['pwd_otp_verified'] = false;

    // Send email
    try {
        include BASE_PATH . 'include/send_mail.php';
        send_password_reset_mail($user['email'], $user['name'], $otp);
        $mail->send();
        echo json_encode(['success' => true, 'email' => substr($user['email'], 0, 3) . '***@' . explode('@', $user['email'])[1]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Could not send email. Please try again.']);
    }
    exit;
}

/* ─────────────────────────────────────────────────────────
   ACTION 2 — verify_otp
   Check the 6-digit code
───────────────────────────────────────────────────────── */
if ($action === 'verify_otp') {
    $entered = trim($_POST['otp'] ?? '');

    if (empty($_SESSION['pwd_otp'])) {
        echo json_encode(['success' => false, 'error' => 'No OTP found. Please request a new one.']);
        exit;
    }
    if (time() > ($_SESSION['pwd_otp_expire'] ?? 0)) {
        unset($_SESSION['pwd_otp'], $_SESSION['pwd_otp_expire'], $_SESSION['pwd_otp_verified']);
        echo json_encode(['success' => false, 'error' => 'OTP has expired. Please request a new one.']);
        exit;
    }
    if ($entered !== $_SESSION['pwd_otp']) {
        echo json_encode(['success' => false, 'error' => 'Incorrect OTP. Please try again.']);
        exit;
    }

    $_SESSION['pwd_otp_verified'] = true;
    echo json_encode(['success' => true]);
    exit;
}

/* ─────────────────────────────────────────────────────────
   ACTION 3 — change_password
   Only allowed after OTP is verified in this session
───────────────────────────────────────────────────────── */
if ($action === 'change_password') {
    if (empty($_SESSION['pwd_otp_verified'])) {
        echo json_encode(['success' => false, 'error' => 'OTP not verified. Please complete verification first.']);
        exit;
    }

    $new_pwd     = $_POST['new_password'] ?? '';
    $confirm_pwd = $_POST['confirm_password'] ?? '';

    if (strlen($new_pwd) < 8) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters.']);
        exit;
    }
    if ($new_pwd !== $confirm_pwd) {
        echo json_encode(['success' => false, 'error' => 'Passwords do not match.']);
        exit;
    }

    $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
    $hashed_esc = mysqli_real_escape_string($conn, $hashed);
    $upd = mysqli_query($conn, "UPDATE users SET password = '$hashed_esc' WHERE id = $user_id");

    if ($upd) {
        // Clear OTP session vars
        unset($_SESSION['pwd_otp'], $_SESSION['pwd_otp_expire'], $_SESSION['pwd_otp_verified']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error. Please try again.']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
exit;
