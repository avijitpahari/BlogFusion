<?php
include "db.php";
global $conn;
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$image=$data['profile_image'];
?>