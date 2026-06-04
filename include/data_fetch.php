<?php


include 'db.php';
global $conn;

function data_featch($conn,$user_id){
    $user_id = (int)$user_id;
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if(!$result){
        die("Query Failed: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($result);
    if (!$data) {
        return [
            'image' => '../upload/profile-images/default.png',
            'data' => null
        ];
    }

    $image='../'.$data["profile_image"];

    return [
        'image'=> $image,
        'data'=>$data
    ];
}

// Typo alias to prevent breaking imports
if (!function_exists('data_fetch')) {
    function data_fetch($conn, $user_id) {
        return data_featch($conn, $user_id);
    }
}

function data($conn,$table_name,$id){
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
        die("Invalid Table Name");
    }
    $id = (int)$id;
    $query = "SELECT * FROM `$table_name` WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    if(!$result){
        die("Query Failed: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);
}

function total($conn,$table_name){
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
        die("Invalid Table Name");
    }
    $query = "SELECT count(*) as total FROM `$table_name`";
    $result = mysqli_query($conn, $query);

    if(!$result){
        die("Query Failed: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);
}

function alldetails($table_name){
    global $conn;
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
        return false;
    }
    $sql="SELECT * FROM `{$table_name}` ORDER BY id ASC";
    $run=mysqli_query($conn,$sql);
    if($run){
        if(mysqli_num_rows($run)){
            return mysqli_fetch_all($run,MYSQLI_ASSOC);
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function check($table_name,$column,$value){
    global $conn;
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name) || !preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
        return false;
    }
    $value_escaped = mysqli_real_escape_string($conn, $value);
    $sql="SELECT * FROM `{$table_name}` where `{$column}`='{$value_escaped}'";
    $run=mysqli_query($conn,$sql);
    if($run){
        if(mysqli_num_rows($run)){
            return true;
        }else{
            return false;
        }
    }
    return false;
}

function delete($table_name,$id){
    global $conn;
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
        return false;
    }
    $id = (int)$id;
    $sql="DELETE FROM `{$table_name}` where id='$id'";
    $run=mysqli_query($conn,$sql);
    if ($run) {
        return true;
    } else {
        return false;
    }
}



function latest_posts($limit = 10){
    global $conn;

    $sql = "
        SELECT 
            posts.*,
            users.name AS author_name,
            categories.name AS category_name

        FROM posts

        LEFT JOIN users
        ON posts.author_id = users.id

        LEFT JOIN categories
        ON posts.category_id = categories.id

        WHERE posts.status='published'

        ORDER BY posts.id DESC
        LIMIT $limit
    ";

    $run = mysqli_query($conn,$sql);

    $data = [];

    if($run){
        while($row = mysqli_fetch_assoc($run)){
            $data[] = $row;
        }
    }

    return $data;
}


function single_post_slug($slug){

    global $conn;

    $slug = mysqli_real_escape_string($conn,$slug);

    $sql = "
        SELECT
            posts.*,
            users.name AS author_name,
            categories.name AS category_name

        FROM posts

        LEFT JOIN users
        ON posts.author_id = users.id

        LEFT JOIN categories
        ON posts.category_id = categories.id

        WHERE posts.slug='$slug'
        LIMIT 1
    ";

    $run = mysqli_query($conn,$sql);

    return mysqli_fetch_assoc($run);
}


?>