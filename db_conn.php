<?php

// $sname = "localhost";
// $uname = "root";
$sname = "classmysql.engr.oregonstate.edu";
$uname = "capstone_2024_chapmaj2";
$password = "7183";
$db_name = "capstone_2024_chapmaj2";

// Specify the socket path
// $socket = "/path/to/mysql.sock";
$conn = mysqli_connect($sname, $uname, $password, $db_name);

// $socket = ini_get("mysqli.default_socket");

// $conn = mysqli_connect($sname, $uname, $password, $db_name, null, $socket);

if (!$conn) {
    echo "DB Connection Fail";
}
