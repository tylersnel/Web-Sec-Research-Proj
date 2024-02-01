<?php
session_start();
include "db_conn.php";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Smaug Bank</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="app.css">
</head>

<body>
  <div class="container">
    <h2>Create an Account</h2>

    <!-- <form action="home.php" method="POST"> -->
    <!-- <form action="process_signup.php" method="POST"> -->
    <!-- <form action="login.php" method="POST"> -->
    <form action="signup.php" method="POST">
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
      <?php
      if (isset($_POST['submit'])) {

        $uname = $_POST['user_name'];
        // $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
        $pass = $_POST['password'];
        $password_repeat = $_POST['password_repeat'];
        $name = $_POST['name'];
        $account_total = 1000;

        $errors = array();

        if (empty($_POST['user_name'])) {
          //    die("Location: index.php?error= User Name is required");
          //    header("Location: signup.php");
          array_push($errors, "User Name is required");
          //    header("Location: signup.php");
          //    exit();
        }
        if (empty($_POST['password'])) {
          //  die("Location: index.php?error= Password is required");
          array_push($errors, "Password is required");
        }

        if (empty($_POST['password_repeat'])) {
          //  die("Location: index.php?error= Password Repeat is required");
          array_push($errors, "Password Repeat is required");
        }

        if (empty($_POST['name'])) {
          //  die("Location: index.php?error= First Name on Account is required");
          array_push($errors, "Name is required");
        }

        // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
        //     die("password must have a letter");
        // }

        if ($_POST["password"] !== $_POST["password_repeat"]) {
          // die("passwords do not match. try again");
          array_push($errors, "Passwords do not match");
        }

        if (count($errors) > 0) {
          foreach ($errors as  $error) {
            // header("Location: signup.php");
            echo "<div class='error'>$error</div>";
            // exit();
          }
        } else {
          $stmt = $conn->prepare("INSERT INTO `users`(`user_name`, `password`, `name`, `account_total`) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $uname, $pass, $name, $account_total);

          if ($stmt->execute()) {
            // header("Location: home.php");
            // exit();
            echo "<div class='success'>Account Created! Go back to log in</div>";

          } else {
            echo "<div class='error'>Error: Registration failed. Please try again.</div>";
          }

          $stmt->close();
          $conn->close();
        }
      }
      ?>
    </form>
    <a href="index.php">Back to log in</a>
  </div>
</body>

</html>