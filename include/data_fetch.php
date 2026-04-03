<?php


include 'db.php';
global $conn;

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

function alldetails($table_name){
    global $conn;
    $sql="SELECT * FROM {$table_name} ORDER BY id ASC";
    $run=mysqli_query($conn,$sql);
    if($run){
        if(mysqli_num_rows($run)){
            return mysqli_fetch_all($run,MYSQLI_ASSOC);
        }else{
            return false;
        }
    }else{
        return false;
    }

}

function delete($table_name,$id){
    $sql="DELETE FROM {$table_name} where id='$id'";
}

?>