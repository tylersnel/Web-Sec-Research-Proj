<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in
// if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_name'])) {
    // Include the database connection script
    include "db_conn.php";
    // function is a work in progress. This function is supposed to insert admin changes into the database so
    // that the admin can keep track of changes made to the database for access control and auditing purposes.
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
        <script>
            function confirmDelete() {
                return confirm("Are you sure you want to delete this user?");
            }
        </script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>

    <body>
        <!-- <div class="container-fluid"> -->
        <div class="container border bg-light text-center shadow-lg">
            <h1 class="center-text">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
            <br><br>

            <?php
            // Check if there are any users
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="row border border-black my-2">
                        <h2>User ID: <?php echo htmlspecialchars($row['id']); ?></h2>
                        <p>User Name: <?php echo htmlspecialchars($row['user_name']); ?></p>
                        <p>Name: <?php echo htmlspecialchars($row['name']); ?></p>
                        <p>Account Total: <?php echo htmlspecialchars($row['account_total']); ?></p>
                        <!-- Add edit and delete buttons -->

                        <!-- <a href="edit_user.php?id=<?php echo $row['id']; ?>" onclick="insertAdminChange('Edit', <?php echo $_SESSION['id']; ?>, <?php echo $row['id']; ?>)">Edit</a>
                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="insertAdminChange('Delete', <?php echo $_SESSION['id']; ?>, <?php echo $row['id']; ?>)">Delete</a> -->

                        <!-- <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a> -->
                        <button class="text-primary my-2"><a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a></button>
                        <br>
                        <!-- <a href="delete_user.php?id=<?php echo $row['id']; ?>">Delete</a> -->
                        <button class="my-2 bg-opacity-50 bg-danger border-danger border-opacity-50"><a onclick='return confirmDelete();' href="delete_user.php?id=<?php echo $row['id']; ?>">Delete</a></button>
                        <br>
                        <br>
                    </div>
            <?php
                }
            } else {
                echo "No users found.";
            }
            ?>

            <br><br>
            <button class="text-primary"><a href="logout.php">Logout</a></button>
            <!-- <a href="logout.php">Logout</a> -->
        </div>
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