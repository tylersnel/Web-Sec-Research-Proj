 <!DOCTYPE html>
  <html>
  <head>
  <meta charset="UTF-8">
  <title>Smaug Bank</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="app.css">
  </head>
  <body>

    <h2>Create an Account</h2>

    <form action="home.php" method="POST">
    <!-- <form action="login.php" method="POST"> -->
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
  <?php
  // session_start();
  // include "db_conn.php";

  //   if (isset($_POST['submit'])) {

  //     $uname = $_POST['user_name'];

  //     $pass = $_POST['password'];

  //     $name = $_POST['name'];


  //     $sql = "INSERT INTO `users`(`user_name`, `password`, `name`) VALUES ('$uname','$pass','$name')";

  //     $result = $conn->query($sql);

  //     if ($result == TRUE) {

  //       echo "New record created successfully.";

  //     }else{

  //       echo "Error:". $sql . "<br>". $conn->error;

  //     } 

  //     $conn->close(); 

  //   }
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

  //    // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
  //    //     die("password must have a letter");
  //    // }

  //    if ($_POST["password"] !== $_POST["password_repeat"]) {
  //      die("passwords do not match. try again");
  //    }

  ?>