<?php

session_start();
include "db_conn.php";


if (isset($_POST["submit"])) {

    $uname = $_POST["user_name"];
    // $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $pass = $_POST["password"];
    $password_repeat = $_POST["password_repeat"];
    $name = $_POST["name"];
    $account_total = 1000;



   // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
   //     die("password must have a letter");
   // }

   // if ($_POST["password"] !== $_POST["password_repeat"]) {
   //   die("passwords do not match. try again");
   // }

    $stmt = $conn->prepare("INSERT INTO `users`(`user_name`, `password`, `name`, `account_total`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $uname, $pass, $name, $account_total);

    if ($stmt->execute()) {
        header("Location: home.php");
        exit();  
    } else {
        echo "Error: Registration failed. Please try again.";
    }

    $stmt->close();
    $conn->close();

}