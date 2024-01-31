<?php
if (empty($_POST['user_name'])) {
    // die("Location: index.php?error= User Name is required");
    // echo "User Name is required";
    header("Location: signup.php");
    exit();
}

// if (empty($_POST['password'])) {
//     die("Location: index.php?error= Password is required");
// }

// if (empty($_POST['password_repeat'])) {
//     die("Location: index.php?error= Password Repeat is required");
// }

// if (empty($_POST['name'])) {
//     die("Location: index.php?error= First Name on Account is required");
// }

//    // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
//    //     die("password must have a letter");
//    // }

//    if ($_POST["password"] !== $_POST["password_repeat"]) {
//      die("passwords do not match. try again");
//    }

session_start();
include "db_conn.php";

if (isset($_POST['submit'])) {

    $uname = $_POST['user_name'];

    $pass = $_POST['password'];

    $name = $_POST['name'];


    $sql = "INSERT INTO `users`(`user_name`, `password`, `name`) VALUES ('$uname','$pass','$name')";

    $result = $conn->query($sql);

    if ($result == TRUE) {

        // echo "New record created successfully.";
        header("Location: home.php");
        exit();
    } else {

        echo "Error:" . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
