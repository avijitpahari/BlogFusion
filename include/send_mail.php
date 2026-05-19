<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$env = parse_ini_file("../.env");

require '../vendor/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

// try {
//Server settings
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
$mail->isSMTP();                                            //Send using SMTP
$mail->Host = 'smtp.gmail.com';                             //Set the SMTP server to send through
$mail->SMTPAuth = true;                                    //Enable SMTP authentication
$mail->Username = $env['EMAIL'];;                           //SMTP username
$mail->Password = $env['PASSWORD'];                               //SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
$mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
$mail->setFrom('avijitpahari867@gmail.com', 'Blog Fusion');

// } catch (Exception $e) {
// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }
function send_mail($email, $name, $otp)
{
    global $mail;
    //$mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
    $mail->addAddress($email);               //Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
    $otp = str_split($otp);
    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('../upload/profile-images/1773820904_Screenshot 2025-11-07 135403.png', 'new.jpg');    //Optional name
    $mail->addEmbeddedImage(
        '../upload/site_image/logo2.png', // image path
        'logoimg' // cid name
    );
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f9fc; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f6f9fc; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 500px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
                    
                    <tr>
                        <td align="center" style="padding: 40px 0 20px 0;">
                            <div style="background-color: #e4d3ff; width: 50px; height: 50px; border-radius: 12px; display: inline-block;">
                                <img src="cid:logoimg" width="32" height="32" style="padding: 9px; display: block;" alt="Logo">
                            </div>
                            <h2 style="margin: 15px 0 0 0; color: #1f2937; font-size: 30px;">Blog Fusion</h2>
                            <!-- <p style="margin: 5px 0 0 0; font-size: 11px; color: #9ca3af; letter-spacing: 2px; text-transform: uppercase;">The Luminous Editor</p> -->
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 10px 40px;">
                            <h3 style="color: #111827; font-size: 20px; margin-bottom: 10px;">Verify your identity</h3>
                            <p style="color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0;">
                                Hello <span style="color: #7c3aed; font-weight: bold;">' . $name . '</span>, to finalize your account setup, please enter the 6-digit code below.
                            </p>
                            <p style="color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0;">
                                <span style="color: #7c3aed; font-weight: bold;">OTP Expire in 10 minutes.</span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 30px 20px;">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 0 5px;">
                                        <div align="center" style="width: 45px; height: 55px; line-height: 55px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; font-weight: bold; color: #7c3aed; background-color: #f9fafb;">' . $otp[0] . '</div>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <div  align="center" style="width: 45px; height: 55px; line-height: 55px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; font-weight: bold; color: #7c3aed; background-color: #f9fafb;">' . $otp[1] . '</div>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <div align="center" style="width: 45px; height: 55px; line-height: 55px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; font-weight: bold; color: #7c3aed; background-color: #f9fafb;">' . $otp[2] . '</div>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <div align="center" style="width: 45px; height: 55px; line-height: 55px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; font-weight: bold; color: #7c3aed; background-color: #f9fafb;">' . $otp[3] . '</div>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <div align="center" style="width: 45px; height: 55px; line-height: 55px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; font-weight: bold; color: #7c3aed; background-color: #f9fafb;">' . $otp[4] . '</div>
                                    </td>
                                    <td style="padding: 0 5px;">
                                        <div align="center" style="width: 45px; height: 55px; line-height: 55px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 24px; font-weight: bold; color: #7c3aed; background-color: #f9fafb;">' . $otp[5] . '</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <a href="#" style="background-color: #7c3aed; color: #ffffff; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px; display: inline-block;">
                                Copy OTP: ' . $otp[0] . $otp[1] . $otp[2] . $otp[3] . $otp[4] . $otp[5] . ' &rarr;
                            </a>
                        </td>
                    </tr> -->

                    <tr>
                        <td align="center" style="padding: 30px 40px; background-color: #ffffff; border-top: 1px solid #f3f4f6;">
                            <!-- <p style="margin: 0; font-size: 11px; color: #9ca3af; letter-spacing: 1px; text-transform: uppercase;">DIDN\'T RECEIVE A CODE?</p>
                            <a href="#" style="color: #7c3aed; font-size: 14px; text-decoration: none; font-weight: 600; margin-top: 8px; display: inline-block;">Resend Verification Code</a> -->
                            <p style="margin: 20px 0 0 0; font-size: 12px; color: #9ca3af; line-height: 1.4;">
                                &copy; 2024 Blog Fusion. All rights reserved.<br>
                                Sent with care to <span style="color: #7c3aed;">avijitpahari867@gmail.com</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    //$send_mail=$mail->send();
    //echo 'Message has been sent';
    //return $send_mail;
}
$name = $_POST['name'];
$email = $_POST['email'];
$otp = $_POST['otp'];
session_start();
$_SESSION['otp'] = $otp;
send_mail($email, $name, $otp);
if ($mail->send()) {
    echo "success";
} else {
    echo "error";
}

?>