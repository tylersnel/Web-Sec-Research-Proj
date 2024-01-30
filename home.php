<?php 
// session_start();

// if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
     // if (empty($_POST['user_name'])) {
     //      die("Location: index.php?error= User Name is required");
     //      // echo "Location: index.php?error= User Name is required";
     //    }
        
     //    if (empty($_POST['password'])) {
     //      die("Location: index.php?error= Password is required");
     //    }
        
     //    if (empty($_POST['password_repeat'])) {
     //      die("Location: index.php?error= Password Repeat is required");
     //    }
        
     //    if (empty($_POST['name'])) {
     //      die("Location: index.php?error= First Name on Account is required");
     //    }
        
        // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
        //     die("password must have a letter");
        // }
        
     //    if ($_POST["password"] !== $_POST["password_repeat"]) {
     //      die("passwords do not match. try again");
     //    }
        
     session_start();
     include "db_conn.php";
     
     if (isset($_POST['submit'])) {
     
       $user_name = $_POST['user_name'];
     
       $pass = $_POST['password'];
     
       $name = $_POST['name'];
     
     
       $sql = "INSERT INTO `users`(`user_name`, `password`, `name`) VALUES ('$user_name','$pass','$name')";
     
       $result = $conn->query($sql);
     
       if ($result == TRUE) {
     //   if ($result != TRUE) {
     
         $name = $_POST["name"];
         echo"Hello, ". $name ."<br>";
     //     echo $_SESSION['name'];
       } else {
     
         echo "Error:" . $sql . "<br>" . $conn->error;
       }
     
       $conn->close();
     }
     if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {

 ?>
<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
     <h1>Hello, <?php echo $_SESSION['name']; ?></h1>  
     <!-- <a href="logout.php">Logout</a> -->
     <a href="index.php">Logout</a>
</body>
</html>

<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>