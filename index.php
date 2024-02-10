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
            //updated to prevent injection
            $uname = mysqli_real_escape_string($conn, $_POST['user_name']);
            $pass = mysqli_real_escape_string($conn, $_POST['password']);
        
            $errors = array();
        
            if (empty($uname)) {
                array_push($errors, "User Name is required");
            }
            if (empty($pass)) {
                array_push($errors, "Password is required");
            }
            if (count($errors) > 0) {
                foreach ($errors as  $error) {
                    echo "<div class='error'>$error</div>";
                }
            } else {
                //updated to prevent injection. Separates SQL code from user input
                $sql = "SELECT * FROM users WHERE user_name=? AND password=?";
            
                // added to prevent injection
                $stmt = $conn->prepare($sql);
                
                //added to prevent injection
                if ($stmt) {
                    $stmt->bind_param("ss", $uname, $pass);
                
                    $stmt->execute();
        
                    $result = $stmt->get_result();
        
                    if ($result->num_rows === 1) {
                        $row = $result->fetch_assoc();
        
                        // User authenticated successfully
                        // Start session and redirect to home.php
                        session_start();
                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['account_total'] = $row['account_total'];
        
                        // Additional query for transaction table
                        //updated to prevent injection
                        $fk_Id = $row['id'];
                        $additionalQuery = "SELECT * FROM transactions WHERE accountid=?";
                        $stmt = $conn->prepare($additionalQuery);
                        $stmt->bind_param("i", $fk_Id);
                        $stmt->execute();
                        $additionalResult = $stmt->get_result();
        
                        if ($additionalResult->num_rows > 0) {
                            $additionalData = $additionalResult->fetch_assoc();
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
            }else {
                // Error in prepared statement
                echo "Error: " . $conn->error;
            }
            $stmt->close();
        } 
        }
        $conn->close();
        ?>

        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>

    <div class="quote">
        <h1>"My armour is like tenfold shields, my teeth are swords, my claws spears, the shock of my tail is a thunderbolt, my wings a hurricane, and my breath death!"</h1>
        <h1> Smaug, Founder and CEO</h1>
    </div>

</body>

</html>