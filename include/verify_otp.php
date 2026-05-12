<?php
session_start();

$user_otp = $_POST['otp'];

// expiry check (10 min)
if(time() - $_SESSION['otp_time'] > 600){
    echo "expired";
    exit;
}

if($_SESSION['otp'] == $user_otp){
    $_SESSION['verified'] = true;
    echo "success";
}else{
    echo "failed";
}
?>