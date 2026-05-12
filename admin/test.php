<?php
include "../include/db.php";


$query = "
SELECT c.*,p.title,COUNT(*) as total FROM comments c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = 1 ORDER BY c.created_at DESC;
";

$result = mysqli_query($conn, $query);
$comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach ($comments as $c) {
    echo "<h4>{$c['title']}</h4>";
    echo "<p>{$c['comment']}</p>";
}
echo $comments['total'];


?>