<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in
if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    // Include the database connection script
    include "db_conn.php";

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
                    <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a href="delete_user.php?id=<?php echo $row['id']; ?>">Delete</a>
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