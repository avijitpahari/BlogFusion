<?php
date_default_timezone_set('Asia/Kolkata');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogfusion";

$conn = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn, 'utf8mb4');
// if (!$conn) {
//     die("<script> console.log('Connection failed: ') </script>");
// } else {
//     echo "<script>// console.log('Database Connected Successfully!')</script>";
// }

// Initialize notifications table only once to prevent connection overhead
if (!file_exists(__DIR__ . '/.notifications_installed')) {
    $setup_query = "
    CREATE TABLE IF NOT EXISTS `notifications` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT NOT NULL,
      `title` VARCHAR(255) NOT NULL,
      `message` TEXT NOT NULL,
      `type` VARCHAR(50) NOT NULL,
      `is_read` TINYINT(1) DEFAULT 0,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    if (mysqli_query($conn, $setup_query)) {
        @file_put_contents(__DIR__ . '/.notifications_installed', '1');
    }
}


if (!function_exists('add_notification')) {
    function add_notification($conn, $user_id, $title, $message, $type = 'system') {
        $user_id = (int)$user_id;
        $title = mysqli_real_escape_string($conn, $title);
        $message = mysqli_real_escape_string($conn, $message);
        $type = mysqli_real_escape_string($conn, $type);
        
        $query = "INSERT INTO notifications (user_id, title, message, type) VALUES ($user_id, '$title', '$message', '$type')";
        return mysqli_query($conn, $query);
    }
}
?>