<?php
session_start();
include "db_conn.php";
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Smaug Bank</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="app.css">
<style>

</style>

<body>

    <!-- <img src="img_la.jpg" alt="LA" style="width:100%"> -->

    <div class="login-container">
        <h1>Smaug Bank</h1>
        <h2 class="main-login">Online Banking Login</h2>


        <!-- <form action="login.php" method="post" class="login-form"> -->
        <form action="index.php" method="post" class="login-form">
            <input type="text" name="user_name" class="form-input" placeholder="Username">
            <input type="password" name="password" class="form-input" placeholder="Password">
            <button type="submit" class="login-btn">Login</button>
        </form>
        <?php
        if (isset($_POST['user_name']) && isset($_POST['password'])) {
            $uname = $_POST['user_name'];
            $pass = $_POST['password'];
        
            $errors = array();
        
            if (empty($uname)) {
                // header("Location: index.php?error=User Name is required");
                // header("Location: index.php?<div class='error'>User Name is required</div>");
                // echo "<div class='error'>User Name is required</div>";
                // exit();
                array_push($errors, "User Name is required");
            }
            if (empty($pass)) {
                // header("Location: index.php?error=Password is required");
                // echo "<div class='error'>Password is required</div>";
                // exit();
                array_push($errors, "Password is required");
            }
            if (count($errors) > 0) {
                foreach ($errors as  $error) {
                    echo "<div class='error'>$error</div>";
                }
            } else {
                $sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass'";
        
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) === 1) {
                    $row = mysqli_fetch_assoc($result);

                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['account_total'] = $row['account_total'];
        
                        // Additional query for transaction table
                        // Additional query to retrieve data from transactions table based on the account ID
                        $fk_Id = $row['id'];
                        $additionalQuery = "SELECT * FROM transactions WHERE accountid='$fk_Id'";
                        $additionalResult = mysqli_query($conn, $additionalQuery);
        
                        if ($additionalResult) {
                            $additionalData = mysqli_fetch_assoc($additionalResult);
                            // Process the additional data as needed
                            $_SESSION['amount'] = $additionalData['amount'];
                            $_SESSION['transactionID'] = $additionalData['transactionID'];
                            $_SESSION['date'] = $additionalData['date'];
                        }
        
        
                        header("Location: home.php");
                        exit();

                } else {
                    header("Location: index.php?error=not true mysqli_num_rows(result) === 1");
                    exit();
                }
            }
        }
        ?>

        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>

    <div class="quote">
        <h1>"My armour is like tenfold shields, my teeth are swords, my claws spears, the shock of my tail is a thunderbolt, my wings a hurricane, and my breath death!"</h1>
        <h1> Smaug, Founder and CEO</h1>
    </div>

</body>

</html>