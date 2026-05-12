<?php
header('Content-Type: application/json');

if (isset($_FILES['file'])) {

    $file = $_FILES['file'];

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . "." . $ext;

    $uploadPath = "uploads/" . $filename;

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {

        echo json_encode([
            "location" => $uploadPath
        ]);

    } else {
        echo json_encode([
            "error" => "Upload failed"
        ]);
    }

} else {
    echo json_encode([
        "error" => "No file received"
    ]);
}