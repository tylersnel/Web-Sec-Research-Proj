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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <div class="container text-center border bg-light shadow-lg">
    <h2>Create an Account</h2>

    <!-- <form action="home.php" method="POST"> -->
    <!-- <form action="process_signup.php" method="POST"> -->
    <!-- <form action="login.php" method="POST"> -->
    <form action="signup.php" method="POST">
      <fieldset>
        <legend>User information:</legend>
        User name:<br>
        <input type="text" name="user_name" value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : '' ?>">
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
        <input class="btn btn-primary my-1" type="submit" name="submit" value="submit" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
      </fieldset>
      <?php
      if (isset($_POST['submit'])) {

        $uname = $_POST['user_name'];
        $pass = $_POST['password'];  // Hash the password
        // $pass = $_POST['password'];
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

        //send to password validation check
        if (passwordCheck($pass) === false){
          array_push($errors, "Password must be at least 12 characters, can't be a common password, must have one capital and lowercase letter, 
          number, and special character");
        }

        if (count($errors) > 0) {
          foreach ($errors as  $error) {
            // header("Location: signup.php");
            echo "<div class='error'>$error</div>";
            // exit();
          }
        } else {
          $pass=password_hash($pass, PASSWORD_DEFAULT); // HASH password
          $stmt = $conn->prepare("INSERT INTO `users`(`user_name`, `password`, `name`, `account_total`) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $uname, $pass, $name, $account_total);

          if ($stmt->execute()) {
            // header("Location: home.php");
            // exit();
            echo "<div class='success alert alert-success' role='alert'>Account Created! Go back to log in</div>";

          } else {
            echo "<div class='error'>Error: Registration failed. Please try again.</div>";
          }

          $stmt->close();
          $conn->close();
        }
      }
      ?>
    </form>
    <!-- <a href="index.php">Back to log in</a> -->
    <button class="text-primary my-5"><a href="index.php">Back to log in</a></button>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  </div>
</body>

</html>


<?php
function passwordCheck($userPass){
  if (strlen($userPass)<12){
    return false;
  }

  $commonPass = file_get_contents('10k-most-common.txt');
  if (strpos($commonPass,$userPass) !== false ){
    return false;
  }

  //Check for required characters
  $requiredChars = array('uppercase' => false, 'lowercase' => false, 'number' => false, 'special' => false);
    
  // Check for each required character type
  if (preg_match('/[A-Z]/', $userPass)) {
      $requiredChars['uppercase'] = true;
  }
  if (preg_match('/[a-z]/', $userPass)) {
      $requiredChars['lowercase'] = true;
  }
  if (preg_match('/[0-9]/', $userPass)) {
      $requiredChars['number'] = true;
  }
  if (preg_match('/[^A-Za-z0-9]/', $userPass)) {
      $requiredChars['special'] = true;
  }
  
  // Check if all required character types are present
  foreach ($requiredChars as $requiredChar) {
      if (!$requiredChar) {
          // If any required character type is missing, return false
          return false;
      }
  }

}

?>