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
                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['account_total'] = $row['account_total'];
                        $fk_Id = $row['id'];                        
                        
                        //checks if account is locked even if login sucessful 
                        account_lock_check($row['user_name'], $conn);
                    
                        // Additional query for transaction table
                        //updated to prevent injection
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

                        //rest of failed login attemtps
                        $stmt = $conn->prepare("UPDATE users SET failed_logins = 0 WHERE id = $fk_Id");
				                $stmt->execute();
                        header("Location: home.php");
                        exit();
                } else {

                    //checks if account is locked
                    account_lock_check($uname, $conn);
                    //check if failed login attempt on username                 
                    account_failed_attempts($uname,$conn);
                    header("Location: index.php?error=not true mysqli_num_rows(result) === 1");
                    exit();   
                        
                    }
            }else {
                // Error in prepared statement
                echo "Error: " . $conn->error;
                }
                 //$stmt->close();
            }
        }
        $conn->close();
        ?>

        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>

    <div class="centered">
        <p>Administrator? <a href="admin_login.php">Login</a>.</p>
    </div>
    
    <div class="quote">
        <br>
        <br>
        <h1>"My armour is like tenfold shields, my teeth are swords, my claws spears, the shock of my tail is a thunderbolt, my wings a hurricane, and my breath death!"</h1>
        <h1> Smaug, Founder and CEO</h1>
    </div>

</body>

</html>

<?php
//updates failed attempts entity in db
function account_failed_attempts($uname,$conn){

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

     $sql = "SELECT * FROM users WHERE user_name=?";
            
     $stmt = $conn->prepare($sql);
    
     if ($stmt) {
         $stmt->bind_param("s", $uname);
     
         $stmt->execute();

         $result = $stmt->get_result();

         if ($result->num_rows === 1) {
             $row = $result->fetch_assoc();
             $stmt = $conn->prepare("UPDATE users SET failed_logins = failed_logins + 1 WHERE id = " . $row['id']);
             $stmt->execute();
            }
        }

    account_lock($row['user_name'], $conn);
}
?>

<?php
//locks account if need be
function account_lock($user_name,$conn){
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT failed_logins FROM users WHERE user_name =?";
            
     $stmt = $conn->prepare($sql);
    
     if ($stmt) {
         $stmt->bind_param("s", $user_name);
     
         $stmt->execute();

         $result = $stmt->get_result();

         if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['failed_logins'] > 4) {
                
                $countdown_expiry = date('Y-m-d H:i:s', strtotime('+1 minutes'));
                $sql = "UPDATE users SET countdown_expiry = ? WHERE user_name = ?";
            
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ss", $countdown_expiry, $user_name);
     
                    $stmt->execute();

                    $result = $stmt->get_result();                   
                }
                if (!$result){
                    echo "Error in countdown timer";
                }
            }
        }
    }
}
?>

<?php
//Checks if account is locked, unlocks if timmer is over
function account_lock_check($user_name,$conn){
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT failed_logins, countdown_expiry FROM users WHERE user_name = ?";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $user_name);
     
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
            if ($row['failed_logins'] > 4) {
                $current_time = date('Y-m-d H:i:s');
                $countdown_expiry = $row['countdown_expiry'];
                
                if ($countdown_expiry && strtotime($countdown_expiry) > time()) {
                    //account lockout still active
                    echo "<script>alert('Account locked. Try again in one minute');</script>";
                    exit();

                } else{
                    $sql = "UPDATE users SET failed_logins = 0 WHERE user_name = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $user_name);
				    $stmt->execute();
                    
                  
                    
                }
        
            }
            
        }
    }
}
?>