<?php 
$con = mysqli_connect("localhost", "id4263465_login", "coinJar1!", "id4263465_login");

//htmlentities($string);
function row_count($result){
    return mysqli_num_rows($result);
}

function fetch_array($result){
    global $con;
    return mysqli_fetch_array($result);
}

function confirm($result) {
    global $con;
    if(!$result){
        die("Query Failed" . mysqli_error($con));
    }
}

function escape($string){
    global $con;
    return mysqli_real_escape_string($con, $string);
}

function query($query) {
    global $con;
    return mysqli_query($con, $query);
}

?>