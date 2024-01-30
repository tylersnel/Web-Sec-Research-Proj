<?php
//  if (empty($_POST['user_name'])) {
// 	die("Location: index.php?error= User Name is required");
// 	// echo "Location: index.php?error= User Name is required";
//   }
  
//   if (empty($_POST['password'])) {
// 	die("Location: index.php?error= Password is required");
//   }
  
//   if (empty($_POST['password_repeat'])) {
// 	die("Location: index.php?error= Password Repeat is required");
//   }
  
//   if (empty($_POST['name'])) {
// 	die("Location: index.php?error= First Name on Account is required");
//   }
  
//   // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
//   //     die("password must have a letter");
//   // }
  
//   if ($_POST["password"] !== $_POST["password_repeat"]) {
// 	die("passwords do not match. try again");
//   }
session_start(); 
include "db_conn.php";


if (isset($_POST['user_name']) && isset($_POST['password'])){
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    if (empty($user_name)) {
		header("Location: index.php?error=User Name is required");
	    exit();
	}else if(empty($password)){
        header("Location: index.php?error=Password is required");
	    exit();
	}else{
		$sql = "SELECT * FROM users WHERE user_name='$user_name' AND password='$password'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
            if ($row['user_name'] === $user_name && $row['password'] === $password) {
            	$_SESSION['user_name'] = $row['user_name'];
            	$_SESSION['name'] = $row['name'];
            	$_SESSION['id'] = $row['id'];
            	header("Location: home.php");
		        exit();
            }else{
				header("Location: index.php?error=Incorect User name or password");
		        exit();
			}
		}else{
			header("Location: index.php?error=Incorect User name or password");
	        exit();
		}
	}

}else{
    header("Location: index.php");
    exit();
}