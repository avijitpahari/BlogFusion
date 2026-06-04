<?php

session_start();

include "../include/db.php";

if(!isset($_SESSION['user_id'])){

    header("Location: ../pages/login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){

    header(
        "Location: ../author/request-category.php"
    );

    exit;
}

$user_id = $_SESSION['user_id'];

$category_name = mysqli_real_escape_string(
    $conn,
    trim($_POST['category_name'])
);

$reason = mysqli_real_escape_string(
    $conn,
    trim($_POST['reason'])
);

if(empty($category_name) || empty($reason)){

    $_SESSION['message'] =
    "Please fill all fields";

    header(
        "Location: ../author/request-category.php"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| slug
|--------------------------------------------------------------------------
*/

$slug = strtolower(
    preg_replace(
        '/[^A-Za-z0-9]+/',
        '-',
        $category_name
    )
);

$slug = trim($slug,'-');


/*
|--------------------------------------------------------------------------
| duplicate request check
|--------------------------------------------------------------------------
*/

$check_request = mysqli_query(
$conn,
"
SELECT *
FROM category_requests
WHERE category_name='$category_name'
AND status='pending'
"
);

if(mysqli_num_rows($check_request)>0){

    $_SESSION['message'] =
    "Category already requested";

    header(
    "Location: ../author/request-category.php"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| category exists
|--------------------------------------------------------------------------
*/

$check_category = mysqli_query(
$conn,
"
SELECT *
FROM categories
WHERE slug='$slug'
"
);

if(mysqli_num_rows($check_category)>0){

    $_SESSION['message'] =
    "Category already exists";

    header(
    "Location: ../author/request-category.php"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| insert
|--------------------------------------------------------------------------
*/

$sql = "
INSERT INTO category_requests
(
author_id,
category_name,
category_slug,
reason,
status
)

VALUES
(
'$user_id',
'$category_name',
'$slug',
'$reason',
'pending'
)
";

if(mysqli_query($conn,$sql)){

    $_SESSION['message'] =
    "Request submitted successfully";

}else{

    $_SESSION['message'] =
    mysqli_error($conn);
}

header(
"Location: ../author/request-category.php"
);

exit;

?>