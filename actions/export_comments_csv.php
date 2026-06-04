<?php
include "../include/session.php";
requireAdmin();
include "../include/db.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=blogfusion_comments_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, ['Comment ID', 'User Name', 'User Email', 'Post Title', 'Comment Text', 'Likes', 'Date Created']);

$query = "SELECT c.id, c.comment, c.like, c.created_at, u.name AS user_name, u.email AS user_email, p.title AS post_title 
          FROM comments c 
          LEFT JOIN users u ON c.user_id = u.id 
          LEFT JOIN posts p ON c.post_id = p.id 
          ORDER BY c.created_at DESC";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'],
            $row['user_name'] ?? 'Guest/Deleted User',
            $row['user_email'] ?? 'N/A',
            $row['post_title'] ?? 'Deleted Post',
            $row['comment'],
            $row['like'],
            $row['created_at']
        ]);
    }
}

fclose($output);
exit();
?>
