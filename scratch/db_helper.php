<?php
include __DIR__ . '/../include/db.php';
global $conn;

$res = mysqli_query($conn, 'SHOW COLUMNS FROM settings');
echo "COLUMNS IN SETTINGS:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

$res = mysqli_query($conn, 'SELECT * FROM settings');
echo "\nROWS IN SETTINGS:\n";
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
