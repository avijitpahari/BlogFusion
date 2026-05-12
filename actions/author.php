<?php
include "../include/db.php";
global $conn;
session_start();



// =========  autor profile update section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profile_update'])) {
    $user_id = $_POST['id'];
    $name = $_POST['name'];
    $new_image = $_FILES['new_image'];
    $old_image = $_POST['old_image'];
    $path = "";
    $file = $_FILES['new_image'];
    if (!empty($_FILES['new_image']['name'])) {
        $tmpname = $file['tmp_name'];
        $imgname = $file['name'];
        if ($imgname) {
            $path = 'upload/profile-images/' . time() . '_' . $imgname;
        }
        move_uploaded_file($tmpname, '../' . $path);
        unlink('../' . $old_image);
    } else {
        $path = $old_image;
    };
    $sql = "UPDATE users set name='$name',profile_image='$path' where id='$user_id'";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if ($_SESSION['user_id'] === $user_id) {
            $_SESSION['user_name'] = $name;
        }
        header("Location: ../author/profile.php");
    } else {
        echo "<script> alert('update unsuccessful');history.back();</script>";
    }
}


// =========  comment delete section  ==========
include "../include/data_fetch.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'comment') {
    $id = $_GET['id'];
    $call = delete('comments', $id);
    if ($call) {
        echo "<script>alert('comment Delete Successful.');window.location.href='../author/comments.php';</script>";
    } else {
        echo "<script>alert('comment Delete Unsuccessful.');window.location.href='../author/comments.php';</script>";
    }
}
































































