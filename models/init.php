<?php ob_start();
session_start();
include("validate_login.php");
include("db.php");
include("functions.php");
if ($con) {
    echo "we are connected";

}else { echo "not connected"; }
?>
