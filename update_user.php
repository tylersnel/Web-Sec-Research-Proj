<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    // header('location: admin_login.php');
    // exit();
}

include 'db_conn.php';

// Retrieve user ID and other values from the form submission
$user_id = $_POST['user_id'];
$uname = mysqli_real_escape_string($conn, $_POST['user_name']);
$pass = mysqli_real_escape_string($conn, $_POST['password']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$account_total = mysqli_real_escape_string($conn, $_POST['account_total']);

// Perform the update query
$sql = "UPDATE users SET user_name=?, password=?, name=?, account_total=? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $uname, $pass, $name, $account_total, $user_id);
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    header('location: admin_home.php');
    exit();
} else {
    echo "Update failed. Please try again.";
}

$stmt->close();
$conn->close();

