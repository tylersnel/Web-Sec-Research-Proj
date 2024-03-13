
<?php

$sname = "localhost";
$uname = "root";
// $sname = "classmysql.engr.oregonstate.edu";
// $uname = "capstone_2024_chapmaj2";
$password = "";
// $db_name = "capstone_2024_chapmaj2";
$db_name = "login_db";

// Specify the socket path
// $socket = "/path/to/mysql.sock";
$conn = mysqli_connect($sname, $uname, $password, $db_name);

// $socket = ini_get("mysqli.default_socket");

// $conn = mysqli_connect($sname, $uname, $password, $db_name, null, $socket);

if (!$conn) {
    echo "DB Connection Fail";
}
