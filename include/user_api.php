<?php
require_once dirname(__DIR__) . '/config.php';
include_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$jsonData = [
    "blogs" => [],
    // "comments" => []
];


// ========= helper =========

function formatViews($views)
{
    if ($views >= 1000000) {
        return round($views / 1000000, 1) . "m";
    }

    if ($views >= 1000) {
        return round($views / 1000, 1) . "k";
    }

    return (string)$views;
}


// ========= nested replies =========

function getReplies($conn, $parent_id)
{
    $replies = [];

    $query = "
    SELECT c.*, u.name
    FROM comments c
    INNER JOIN users u
    ON c.user_id = u.id
    WHERE c.parent_id = '$parent_id'
    ORDER BY c.created_at ASC
    ";

    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {

        $avatar =
            strtoupper(substr($row['name'], 0, 1));

        $reply = [

            "id" => (int)$row['id'],

            "author" => $row['name'],

            "avatar" => $avatar,

            "time" => date(
                "M d, Y h:i A",
                strtotime($row['created_at'])
            ),

            "text" => $row['comment'],

            "likes" => 0,

            "liked" => false,

            "replies" =>
            getReplies(
                $conn,
                $row['id']
            )

        ];

        $replies[] = $reply;
    }

    return $replies;
}



// ========= fetch blogs =========

$query = "
SELECT
p.*,
u.name AS author_name,
u.profile_image AS author_image,
c.slug AS category_slug,
(SELECT COUNT(*) FROM reactions WHERE post_id = p.id) AS reaction_count,
(SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
(SELECT COUNT(*) FROM share WHERE post_id = p.id) AS share_count

FROM posts p

INNER JOIN users u
ON p.author_id = u.id

LEFT JOIN categories c
ON p.category_id = c.id

WHERE p.status='published'

ORDER BY p.created_at DESC
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    // tags decode
    $tags = [];

    if (!empty($row['tag'])) {

        $decoded =
            json_decode(
                $row['tag'],
                true
            );

        if (is_array($decoded)) {
            $tags = $decoded;
        }
    }


    // blog object
    $blog = [

        "id" =>$row['id'],

        "slug" => $row['slug'],

        "title" => $row['title'],

        "cat" =>
        $row['category_slug'] ?? "",

        "author" =>
        $row['author_name'],

        "author_image" =>
        !empty($row['author_image']) ? $row['author_image'] : "upload/profile-images/default.png",

        "date" => date(
            "M d, Y",
            strtotime(
                $row['created_at']
            )
        ),

        "views" =>
        formatViews(
            $row['views'] ?? 0
        ),

        "reaction_count" => (int)($row['reaction_count'] ?? 0),
        "comment_count" => (int)($row['comment_count'] ?? 0),
        "share_count" => (int)($row['share_count'] ?? 0),

        "tags" => $tags,

        "img" =>
        !empty($row['image'])
            ? BASE_URL . $row['image']
            : "",

        "content" =>
        $row['content']

    ];

    $jsonData["blogs"][] = $blog;



    // ========= comments =========

    // $slug = $row['slug'];

    // $jsonData["comments"][$slug] = [];


    // $comment_query = "
    // SELECT c.*, u.name
    // FROM comments c
    // INNER JOIN users u
    // ON c.user_id = u.id
    // WHERE c.post_id='{$row['id']}'
    // AND c.parent_id IS NULL
    // ORDER BY c.created_at DESC
    // ";

    // $comment_result =
    //     mysqli_query(
    //         $conn,
    //         $comment_query
    //     );

    // while (
    //     $comment =
    //     mysqli_fetch_assoc(
    //         $comment_result
    //     )
    // ) {

        // $avatar =
        //     strtoupper(
        //         substr(
        //             $comment['name'],
        //             0,
        //             1
        //         )
        //     );

        // $jsonData["comments"][$slug][] = [

        //     "id" =>
        //     (int)$comment['id'],

        //     "author" =>
        //     $comment['name'],

            // "avatar" =>
            // $avatar,

        //     "time" => date(
        //         "M d, Y h:i A",
        //         strtotime(
        //             $comment[
        //             'created_at'
        //             ]
        //         )
        //     ),

        //     "text" =>
        //     $comment['comment'],

        //     "likes" => 0,

        //     "liked" => false,

        //     "replies" =>
        //     getReplies(
        //         $conn,
        //         $comment['id']
        //     )

        // ];
    // }
}


echo json_encode(
    $jsonData,
    JSON_PRETTY_PRINT
);