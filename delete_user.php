<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_conn.php';
session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
    // header("Location: admin_login.php");
    // exit();
}

// Process user deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Sanitize user ID
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete related records in the transactions table
    $delete_transactions_sql = "DELETE FROM transactions WHERE accountId = '$user_id'";
    if ($conn->query($delete_transactions_sql) === TRUE) {
        // Proceed with deleting the user from the users table
        $delete_user_sql = "DELETE FROM users WHERE id = '$user_id'";
        if ($conn->query($delete_user_sql) === TRUE) {
            // Redirect after deletion
            header("Location: admin_home.php");
            exit();
        } else {
            echo "Error deleting user: " . $conn->error;
        }
    } else {
        echo "Error deleting related transactions: " . $conn->error;
    }
}

// Close database connection
$conn->close();

