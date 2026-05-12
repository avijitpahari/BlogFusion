<?php
include "../include/db.php";



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    // ===========================
    // CHECK USER EMAIL
    // ===========================
    if ($action === "check_email") {

        $email = $_POST['email'];

        $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {

            echo json_encode([
                "status" => "exists",
                "message" => "Email already exists"
            ]);

        } else {

            echo json_encode([
                "status" => "available",
                "message" => "Email available"
            ]);
        }

        exit;
    }
}
?>
