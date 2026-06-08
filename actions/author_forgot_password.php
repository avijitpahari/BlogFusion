<?php
/**
 * actions/author_forgot_password.php
 * AJAX handler for email-based password reset (no login required).
 * Steps:
 *   1. send_reset_otp  – find email, generate OTP, send email
 *   2. verify_reset_otp – verify the 6-digit code
 *   3. reset_password   – update password (requires verified OTP)
 */
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
include BASE_PATH . 'include/db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

/* ─────────────────────────────────────────────────────────
   ACTION 1 — send_reset_otp
───────────────────────────────────────────────────────── */
if ($action === 'send_reset_otp') {
    $email = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Please enter a valid email address.']);
        exit;
    }

    $u_res = mysqli_query($conn, "SELECT id, name, email FROM users WHERE email = '$email' LIMIT 1");
    $user  = mysqli_fetch_assoc($u_res);

    if (!$user) {
        // Don't reveal if email exists — generic success message
        echo json_encode(['success' => true, 'email' => substr($email, 0, 3) . '***@' . explode('@', $email)[1]]);
        exit;
    }

    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $_SESSION['reset_otp']          = $otp;
    $_SESSION['reset_otp_expire']   = time() + 600;
    $_SESSION['reset_otp_user_id']  = (int)$user['id'];
    $_SESSION['reset_otp_verified'] = false;

    try {
        include BASE_PATH . 'include/send_mail.php';
        send_password_reset_mail($user['email'], $user['name'], $otp);
        $mail->send();
        echo json_encode(['success' => true, 'email' => substr($email, 0, 3) . '***@' . explode('@', $email)[1]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Could not send email. Please try again.']);
    }
    exit;
}

/* ─────────────────────────────────────────────────────────
   ACTION 2 — verify_reset_otp
───────────────────────────────────────────────────────── */
if ($action === 'verify_reset_otp') {
    $entered = trim($_POST['otp'] ?? '');

    if (empty($_SESSION['reset_otp'])) {
        echo json_encode(['success' => false, 'error' => 'No OTP found. Please request a new code.']);
        exit;
    }
    if (time() > ($_SESSION['reset_otp_expire'] ?? 0)) {
        unset($_SESSION['reset_otp'], $_SESSION['reset_otp_expire'], $_SESSION['reset_otp_user_id'], $_SESSION['reset_otp_verified']);
        echo json_encode(['success' => false, 'error' => 'OTP has expired. Please request a new one.']);
        exit;
    }
    if ($entered !== $_SESSION['reset_otp']) {
        echo json_encode(['success' => false, 'error' => 'Incorrect OTP. Please check your email and try again.']);
        exit;
    }

    $_SESSION['reset_otp_verified'] = true;
    echo json_encode(['success' => true]);
    exit;
}

/* ─────────────────────────────────────────────────────────
   ACTION 3 — reset_password
───────────────────────────────────────────────────────── */
if ($action === 'reset_password') {
    if (empty($_SESSION['reset_otp_verified'])) {
        echo json_encode(['success' => false, 'error' => 'OTP not verified.']);
        exit;
    }

    $user_id     = (int)($_SESSION['reset_otp_user_id'] ?? 0);
    $new_pwd     = $_POST['new_password'] ?? '';
    $confirm_pwd = $_POST['confirm_password'] ?? '';

    if ($user_id === 0) {
        echo json_encode(['success' => false, 'error' => 'Session expired. Please start again.']);
        exit;
    }
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
        unset($_SESSION['reset_otp'], $_SESSION['reset_otp_expire'], $_SESSION['reset_otp_user_id'], $_SESSION['reset_otp_verified']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error. Please try again.']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action.']);
exit;
