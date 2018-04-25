<?php 
include("functions/init.php");
session_destroy();

if(isset($_COOKIE['email'])){
    unset($_COOKIE['email']);
    
}
redirect("login.php");

?>