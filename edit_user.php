<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    // header('location: admin_login.php');
    // exit();
}

include 'db_conn.php';

// Retrieve user ID from the URL parameter
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    // Redirect if user ID parameter is not provided or empty
    header('location: admin_home.php');
    exit();
}

// Retrieve user information from the database based on the user ID
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    $errors[] = "Error executing query: " . $stmt->error;
} else {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $errors[] = "User not found.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User - Smaug Bank</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1>Edit User</h1>

    <?php if (!empty($errors)) : ?>
        <div class="error">
            <?php foreach ($errors as $error) : ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <form action="update_user.php" method="post">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <label>User Name:</label>
            <input type="text" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>"><br><br>
            <label>Password:</label>
            <input type="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>"><br><br>
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"><br><br>
            <label>Account Total:</label>
            <input type="text" name="account_total" value="<?php echo htmlspecialchars($user['account_total']); ?>"><br><br>
            <input type="submit" value="Update">
        </form>
    <?php endif; ?>
</body>

</html>
