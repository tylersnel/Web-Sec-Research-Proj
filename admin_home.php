<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in
// if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_name'])) {
    // Include the database connection script
    include "db_conn.php";

    // Function to insert admin changes into the database
    function insertAdminChange($conn, $action, $adminId, $affectedUserId)
    {
        $timestamp = date("Y-m-d H:i:s");
        // Sanitize inputs to prevent SQL injection
        $action = $conn->real_escape_string($action);
        $adminId = intval($adminId);
        $affectedUserId = intval($affectedUserId);

        $sql = "INSERT INTO admin_changes (action, admin_id, affected_user_id, timestamp) 
                VALUES ('$action', $adminId, $affectedUserId, '$timestamp')";
        if ($conn->query($sql) === TRUE) {
            echo "New record inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // // Handle AJAX request to insert admin changes
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && isset($_GET["admin_id"]) && isset($_GET["affected_user_id"])) {
        $action = $_GET["action"];
        $adminId = $_GET["admin_id"];
        $affectedUserId = $_GET["affected_user_id"];
        insertAdminChange($conn, $action, $adminId, $affectedUserId);
        exit; // Terminate script execution after handling AJAX request
    }

    // Perform SELECT query to fetch user data
    $sql = "SELECT * FROM `users`";
    $result = $conn->query($sql);

    // Display the admin home page
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Admin Home - Smaug Bank</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>

    <body>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <br><br>

        <?php
        // Check if there are any users
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
        ?>
                <div>
                    <h2>User ID: <?php echo $row['id']; ?></h2>
                    <p>User Name: <?php echo $row['user_name']; ?></p>
                    <p>Name: <?php echo $row['name']; ?></p>
                    <p>Account Total: <?php echo $row['account_total']; ?></p>
                    <!-- Add edit and delete buttons -->

                    <a href="edit_user.php?id=<?php echo $row['id']; ?>" onclick="insertAdminChange('Edit', <?php echo $_SESSION['id']; ?>, <?php echo $row['id']; ?>)">Edit</a>
                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="insertAdminChange('Delete', <?php echo $_SESSION['id']; ?>, <?php echo $row['id']; ?>)">Delete</a>

                </div>
        <?php
            }
        } else {
            echo "No users found.";
        }
        ?>

        <br><br>
        <a href="logout.php">Logout</a>
    </body>

    <script>
        // Function to insert admin changes
        function insertAdminChange(action, adminId, affectedUserId) {
            // AJAX call to insertAdminChange PHP function
            var xhttp = new XMLHttpRequest();
            // xhttp.onreadystatechange = function() {
            //     if (this.readyState == 4 && this.status == 200) {
            //         console.log(this.responseText);
            //     }
            // };
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        console.log("Success: " + this.responseText);
                    } else {
                        console.log("Error: " + this.status);
                    }
                }
            };
            xhttp.open("GET", "insert_admin_change.php?action=" + encodeURIComponent(action) + "&admin_id=" + adminId + "&affected_user_id=" + affectedUserId, true);
            xhttp.send();
        }
    </script>

    </html>
<?php

    // Close the database connection
    $conn->close();
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php");
    exit();
}
?>