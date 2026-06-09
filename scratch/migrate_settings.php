<?php
include __DIR__ . '/../include/db.php';
global $conn;

// 1. Add contact columns if they don't exist
$columns_to_add = [
    'contact_email' => "VARCHAR(150) DEFAULT 'hello@blogfusion.com'",
    'contact_address' => "TEXT",
    'contact_hours' => "VARCHAR(100) DEFAULT 'Mon-Fri, 9am-6pm IST'"
];

foreach ($columns_to_add as $col => $definition) {
    // Check if column exists
    $check = mysqli_query($conn, "SHOW COLUMNS FROM settings LIKE '$col'");
    if (mysqli_num_rows($check) == 0) {
        $alter = mysqli_query($conn, "ALTER TABLE settings ADD COLUMN $col $definition");
        if ($alter) {
            echo "Added column: $col\n";
        } else {
            echo "Failed to add column: $col. Error: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "Column already exists: $col\n";
    }
}

// 2. Check if there is any row in settings
$check_rows = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM settings");
$row_count = mysqli_fetch_assoc($check_rows)['cnt'];

if ($row_count == 0) {
    $insert = mysqli_query($conn, "INSERT INTO settings (site_name, logo, description, contact_email, contact_address, contact_hours) VALUES (
        'Blog Fusion',
        'upload/site_image/logo2.png',
        'Connecting ideas and people. Blog Fusion is your go-to destination for high-quality insights on technology, design, and modern development.',
        'hello@blogfusion.com',
        'Innovation District, Tech City, TC 10101',
        'Mon-Fri, 9am-6pm IST'
    )");
    if ($insert) {
        echo "Inserted default settings row\n";
    } else {
        echo "Failed to insert default settings row. Error: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Settings row already exists\n";
}
?>
