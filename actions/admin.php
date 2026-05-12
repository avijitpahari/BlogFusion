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
        unlink('../' . $old_image);
    } else {
        $path = $old_image;
    };
    $sql = "UPDATE users set name='$name', email='$email',role='$role',profile_image='$path' where id='$user_id'";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if ($_SESSION['user_id'] === $user_id) {
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;
        }
        $page = $_GET['page'];
        $url = '../admin/users.php?page=' . $page;
        header("Location: $url");
    } else {
        echo "<script> alert('update unsuccessful');history.back();</script>";
    }
}


// =========  user create section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $new_image = $_FILES['new_image'];

    $path = "";
    $file = $_FILES['image'];
    if ($file) {
        $tmpname = $file['tmp_name'];
        $imgname = $file['name'];
        if ($imgname) {
            $path = 'upload/profile-images/' . time() . '_' . $imgname;
        }
    }
    ;
    if (empty($path)) {
        $path = 'upload/profile-images/default.png';
    }
    ;
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../admin/users.php?msg=email");
        exit();
    } else {
        $sql = "INSERT INTO users (name, email,password,role,profile_image) VALUES ('$name', '$email','$password','$role','$path')";
        if ($result) {
            if ($file) {
                move_uploaded_file($tmpname, '../' . $path);
            }
        }

        if (mysqli_query($conn, $sql)) {
            header("Location: ../admin/users.php?msg=user_add");
            exit();
        }
    }

}



// =========  user delete section  ==========
include "../include/data_fetch.php";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'user') {
    $id = $_GET['id'];
    $call = delete('users', $id);
    if ($call) {
        echo "<script>alert('User Delete Successful.');window.location.href='../admin/users.php';</script>";
    } else {
        echo "<script>alert('User Delete Unsuccessful.');window.location.href='../admin/users.php';</script>";
    }
}

// =========  category update section  ==========


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category-edit'])) {
    global $conn;
    $id = $_POST['id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    //echo  $name,$slug,$id;
    $sql = "UPDATE categories set name='$name', slug='$slug' where id='$id'";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        $page = $_GET['page'];
        $url = '../admin/categories.php?page=' . $page;
        header("Location: $url");
    } else {
        echo "<script> alert('update unsuccessful');history.back();</script>";
    }
}


// =========  category add section  ==========

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category-add'])) {
    global $conn;
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $query = "SELECT * FROM categories WHERE slug='$slug'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $page = $_GET['page'];
        $url = '../admin/categories.php?page=' . $page;
        header("Location: $url");
    } else {
        $sql = "INSERT INTO categories (name,slug) VALUES ('$name','$slug')";
        if (mysqli_query($conn, $sql)) {
            $page = $_GET['page'];
            $url = '../admin/categories.php?page=' . $page;
            header("Location: $url");
        }
    }

}

// =========  category delete section  ==========

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['btn'] == 'category') {
    $id = $_GET['id'];
    $call = delete('categories', $id);
    if ($call) {
        echo "<script>alert('User Delete Successful.');window.location.href='../admin/categories.php';</script>";
    } else {
        echo "<script>alert('User Delete Unsuccessful.');window.location.href='../admin/categories.php';</script>";
    }
}
?>