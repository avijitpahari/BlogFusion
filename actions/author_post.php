<?php
include '../include/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $description = $_POST['short_description'];
    $content = $_POST['content'];
    //$category_id = $_POST['category_id'];
    $status = $_POST['status'];
    $author_id = $_SESSION['user_id'];

    $path = "";
    $file = $_FILES['blog_image'];
    if ($file) {
        $tmpname = $file['tmp_name'];
        $imgname = $file['name'];
        if ($imgname) {
            $path = 'upload/blog_image/' . time() . '_' . $imgname;
        }
    }
    ;
    if (empty($path)) {
        $path = '';
    }
    ;
    echo   $status;
    //$query = "INSERT INTO posts VALUES ($title, $slug, $description, $content, $path, $category_id, $author_id, $status, $created_at)";
    // $result = mysqli_query($conn, $query);
    // if ($result) {
    //     if ($file) {
    //         move_uploaded_file($tmpname, '../' . $path);
    //     }
    //     header("Location: ../author/my-post.php?msg=posted");
    //     exit();
    // } else{
    //     header("Location: ../author/edit-post.php?msg=post_fail");
    //     exit();
    // }


}
?>