<?php
include "../include/db.php";
global $conn;
session_start();

// =========  user update section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user-update'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $new_image = $_FILES['new_image'];
    $old_image = $_POST['old_image'];
    
    // echo  $name,$email,$role,'<pre>';
    // print_r($new_image);
    $path = "";
    $file = $_FILES['new_image'];
    if (!empty($_FILES['new_image']['name'])) {
        $tmpname = $file['tmp_name'];
        $imgname = $file['name'];
        if ($imgname) {
            $path = 'upload/profile-images/' . time() . '_' . $imgname;
        }
        move_uploaded_file($tmpname, '../' . $path);
        unlink('../'.$old_image);
    } else {
        $path = $old_image;
    }
    ;
    $sql = "UPDATE users set name='$name', email='$email',role='$role',profile_image='$path' where id='$user_id'";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if ($_SESSION['user_id'] === $user_id) {
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;
        }
        $page=$_GET['page'];
        $url='../admin/users.php?page='.$page;
        header("Location: $url");;
    } else {
       echo "<script> alert('update unsuccessful');history.back();</script>";
    }
}
?>