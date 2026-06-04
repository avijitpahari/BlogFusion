<?php
session_start();
include "../include/db.php";
include "../include/send_mail.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address"]);
        exit;
    }

    // Check if email already exists
    $escaped_email = mysqli_real_escape_string($conn, $email);
    $check_query = "SELECT id FROM users WHERE email = '$escaped_email' LIMIT 1";
    $result = mysqli_query($conn, $check_query);
    if ($result && mysqli_num_rows($result) > 0) {
        echo json_encode(["status" => "exists", "message" => "Email already registered"]);
        exit;
    }

    // Generate secure 6-digit OTP
    try {
        $otp = random_int(100000, 999999);
    } catch (Exception $e) {
        $otp = mt_rand(100000, 999999);
    }

    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_time'] = time();

    send_mail($email, $name, $otp);

    if ($mail->send()) {
        echo json_encode(["status" => "success", "message" => "OTP sent successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send email. " . $mail->ErrorInfo]);
    }
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}
?>
