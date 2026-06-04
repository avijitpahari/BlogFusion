<?php
session_start();

$user_otp = isset($_POST['otp']) ? (int)$_POST['otp'] : 0;

if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time'])) {
    echo "failed";
    exit;
}

// expiry check (10 min)
if (time() - (int)$_SESSION['otp_time'] > 600) {
    echo "expired";
    exit;
}

if ((int)$_SESSION['otp'] === $user_otp) {
    $_SESSION['verified'] = true;
    $_SESSION['verified_email'] = $_SESSION['otp_email'] ?? '';
    echo "success";
} else {
    echo "failed";
}
?>