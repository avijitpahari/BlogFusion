<?php

include "include/db.php";
include "include/functions.php";
$year = date("Y");
$month = date("m");
$id = (int)$_GET['id'];

$sql = "
SELECT slug, created_at
FROM posts
WHERE id='$id'
LIMIT 1
";

$result = mysqli_query($conn,$sql);

$post = mysqli_fetch_assoc($result);

if(!$post){
    die("Post not found");
}

$post_time = strtotime($post['created_at']);
$year = date("Y", $post_time);
$month = date("m", $post_time);

header(
    "Location: " . site_url("posts/" . $year . "/" . $month . "/" . $post['slug'])
);

exit;