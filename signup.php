<?php  
session_start();
include "db_conn.php";

  if (isset($_POST['submit'])) {

    $uname = $_POST['user_name'];

    $pass = $_POST['password'];

    $name = $_POST['name'];


    $sql = "INSERT INTO `users`(`user_name`, `password`, `name`) VALUES ('$uname','$pass','$name')";

    $result = $conn->query($sql);

    if ($result == TRUE) {

      echo "New record created successfully.";

    }else{

      echo "Error:". $sql . "<br>". $conn->error;

    } 

    $conn->close(); 

  }

?>

<!DOCTYPE html>

<html>

<body>

<h2>Create an Account</h2>

<form action="process-signup.php" method="POST">

  <fieldset>

    <legend>User information:</legend>

    User name:<br>

    <input type="text" name="user_name">

    <br>

    Password:<br>

    <input type="text" name="password">
    <!-- <input type="password" name="password"> -->

    <br>

    Repeat Password:<br>

    <input type="text" name="password_repeat">
    <!-- <input type="password" name="password_repeat"> -->

    <br>

    First Name on Account:<br>

    <input type="text" name="name">

    <br>


    <input type="submit" name="submit" value="submit">

  </fieldset>

</form>

</body>

</html>