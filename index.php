<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db_conn.php";
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Smaug Bank</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
</style>

<body>
    <div class="container text-center my-5 bg-light shadow-lg">
        <div class="row">
            <div class="col justify-content-center border">
                <h1 class="text-success">Smaug Bank</h1>
                <h2 class="text-success text-opacity-75">Online Banking</h2>
                <h2 class="main-login">Login</h2>

                <!-- <form action="login.php" method="post" class="login-form"> -->
                <form action="index.php" method="post" class="text-center border mx-1">
                    <input type="text" name="user_name" class="form-input my-1" placeholder="Username">
                    <br>
                    <input type="password" name="password" class="form-input" placeholder="Password">
                    <br>
                    <button type="submit" class="btn btn-primary my-3">Login</button>
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
                            echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
                        }
                    } else {
                        //updated to prevent injection. Separates SQL code from user input
                        $sql = "SELECT * FROM users WHERE user_name=?";

                        // added to prevent injection
                        $stmt = $conn->prepare($sql);

                        //added to prevent injection
                        if ($stmt) {
                            $stmt->bind_param("s", $uname);

                            $stmt->execute();

                            $result = $stmt->get_result();

                            if ($result->num_rows === 1) {
                                $row = $result->fetch_assoc();
                                if (password_verify($pass, $row['password'])) {

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
                                    //If password not found                                
                                    //checks if account is locked
                                    account_lock_check($uname, $conn);
                                    //check if failed login attempt on username                 
                                    account_failed_attempts($uname, $conn);
                                    array_push($errors, "$pass password incorrcet. Try again");
                                    if (count($errors) > 0) {
                                        foreach ($errors as  $error) {
                                            echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
                                        }
                                    }
                                    exit();
                                }
                            } else {
                                //If username not found
                                array_push($errors, "$uname username not found. Try again");
                                if (count($errors) > 0) {
                                    foreach ($errors as  $error) {
                                        echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
                                    }
                                }
                                exit();
                            }
                        } else {
                            // Error in prepared statement
                            echo "Error: " . $conn->error;
                        }
                        //$stmt->close();
                    }
                }
                $conn->close();
                ?>

                <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
                <p>Administrator? <a href="admin_login.php">Login</a>.</p>
            </div>
            <div class="col">
                <br>
                <h1 class="">"My armour is like tenfold shields, my teeth are swords, my claws spears, the shock of my tail is a thunderbolt, my wings a hurricane, and my breath death!"</h1>
                <h1 class=""> Smaug, Founder and CEO</h1>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>

</html>

<?php
//updates failed attempts entity in db
function account_failed_attempts($uname, $conn)
{

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
    if ($result->num_rows === 1) {
        account_lock($row['user_name'], $conn);
    }
}
?>

<?php
//locks account if need be
function account_lock($user_name, $conn)
{
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
            }
        }
    }
}
?>

<?php
//Checks if account is locked, unlocks if timmer is over
function account_lock_check($user_name, $conn)
{
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
                } else {
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