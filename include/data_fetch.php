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

function data($conn,$table_name,$id){
    $query = "SELECT * FROM $table_name WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    if(!$result){
        die("Query Failed: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);

}

function total($conn,$table_name){
    $query = "SELECT count(*) as total FROM $table_name";
    $result = mysqli_query($conn, $query);

    if(!$result){
        die("Query Failed: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result);

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

function check($table_name,$column,$value){
    global $conn;
    $sql="SELECT * FROM {$table_name} where {$column}={$value}";
    $run=mysqli_query($conn,$sql);
    if($run){
        if(mysqli_num_rows($run)){
            echo true;
        }else{
            echo false;
        }
    }
}

function delete($table_name,$id){
    global $conn;
    $sql="DELETE FROM {$table_name} where id='$id'";
    $run=mysqli_query($conn,$sql);
    if ($run) {
        return true;
    } else {
        return false;
    }
}



?>