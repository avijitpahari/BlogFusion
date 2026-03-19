<?php




function data_featch($conn,$user_id){
    $query = "SELECT * FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if(!$result){
        die("Query Failed: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($result);

    $image='../'.$data["profile_image"];

    return [
        'image'=> $image,
        'data'=>$data
    ];
}

?>