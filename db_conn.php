<?php

$sname = "localhost";
$uname= "root";
// $user_name = "root";
$password = "";

$db_name = "login_db";

$conn = mysqli_connect($sname, $uname, $password, $db_name);

if (!$conn) {
    echo "DB Connection Fail";
}
