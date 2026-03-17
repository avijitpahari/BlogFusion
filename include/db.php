<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogfusion";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("<script> console.log('Connection failed: ') </script>");
} else {
    echo "<script>// console.log('Database Connected Successfully!')</script>";
}
?>