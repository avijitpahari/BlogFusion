<?php
include "../include/db.php";
global $conn;

// =========  signup section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password=password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role=$_POST['role'];

    $path="";
    $file=$_FILES['image'];
    if($file){
        $tmpname=$file['tmp_name'];
        $imgname=$file['name'];
        if($imgname){
            $path='upload/profile-images/'.time().'_'.$imgname;
        }
    };
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../pages/register.php?msg=exists");
        exit();
    } else {
        $sql = "INSERT INTO users (name, email,password,role,profile_image) VALUES ('$name', '$email','$password','$role','$path')";
        if($result){
            if($file){
                move_uploaded_file($tmpname,'../'.$path);
            }
        }
        
        if (mysqli_query($conn, $sql)) {
            header("Location: ../pages/login.php?msg=registered");
            exit();
        }
    }
}


// =========  login section  =========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password=$_POST['password'];
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result)) {
        $data=mysqli_fetch_assoc($result);
        if(password_verify($password,$data['password'])){
            session_start();
            $_SESSION['user_id']=$data['id'];
            $_SESSION['role']=$data['role'];
            $_SESSION['msg']='login_success';
            if($data['role']=="user"){
                header("Location: ../pages/home.php");
            }elseif($data['role']=="author"){
                //header("Location: ../pages/home.php");
            }elseif($data['role']=="admin"){
                header("Location: ../admin/dashboard.php");
            }
            
            exit;
        }else{
            header("Location: ../pages/login.php?msg=p_not_match");
            exit;
        }
    }else{
        header("Location: ../pages/login.php?msg=u_not_find");
        exit;
    }
}

?>