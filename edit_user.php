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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve user data from the form submission
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $account_total = $_POST['account_total'];

    // Check if any of the fields have changed
    if ($user_name === $user['user_name'] && $password === $user['password'] && $name === $user['name'] && $account_total === $user['account_total']) {
        $message = "No changes were made.";
    } else {
        // Perform the update query
        $sql = "UPDATE users SET user_name=?, password=?, name=?, account_total=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $user_name, $password, $name, $account_total, $user_id);

        if ($stmt->execute()) {
            $message = "User updated successfully.";
        } else {
            $errors[] = "Error updating user: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User - Smaug Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Edit User</h1>

        <?php if (!empty($errors)) : ?>
            <div class="error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <?php if (isset($message)) : ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="" method="post">
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
        <br><a href="admin_home.php">Go back to Admin Home</a>
    </div>
</body>

</html>