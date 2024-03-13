<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_conn.php';

$errors = []; // Initialize an array to store errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = mysqli_real_escape_string($conn, $_POST['name']);
    $pass = mysqli_real_escape_string($conn, $_POST['pwd']);
    $sql = "SELECT * FROM admin WHERE admin_name=? AND pwd=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $uname, $pass);
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                // User authenticated successfully
                // Start session and redirect to admin_home.php
                $_SESSION['admin_name'] = $row['admin_name'];
                $_SESSION['admin_id'] = $row['admin_id'];
                header('location: admin_home.php');
                exit();
            } else {
                $errors[] = "Invalid credentials";
            }
        } else {
            $errors[] = "Error executing the query: " . $conn->error;
        }
    } else {
        $errors[] = "Error in prepared statement: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Admin Login - Smaug Bank</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container text-center my-5 border bg-light shadow-lg">
        <?php
        // Display errors, if any
        if (!empty($errors)) {
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }
        ?>

        <form action="admin_login.php" method="POST">
            <fieldset>
                <h1>
                    <legend>Admin Login:</legend>
                </h1>
                User name:<br>
                <input type="text" name="name">
                <br>
                Password:<br>
                <input type="text" name="pwd">
                <br>
                <input class="btn btn-primary my-1" type="submit" name="submit" value="Submit">
            </fieldset>
        </form>

        <!-- <a href="index.php">Back to log in</a> -->
        <button class="text-primary my-5"><a href="index.php">Back to log in</a></button>
    </div>
</body>

</html>