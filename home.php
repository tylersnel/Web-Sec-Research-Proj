<?php 
// session_start();

<<<<<<< HEAD
if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    include "db_conn.php"; // Include your database connection script

    // Fetch transactions from the database
    $accountId = $_SESSION['id'];
    $transactionsQuery = "SELECT * FROM transactions WHERE accountid = ?";
    $stmt = $conn->prepare($transactionsQuery);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch account total from the database
    $accountTotalQuery = "SELECT account_total FROM users WHERE id = ?";
    $stmtAccountTotal = $conn->prepare($accountTotalQuery);
    $stmtAccountTotal->bind_param("i", $accountId);
    $stmtAccountTotal->execute();
    $resultAccountTotal = $stmtAccountTotal->get_result();
    $rowAccountTotal = $resultAccountTotal->fetch_assoc();
    $accountTotal = $rowAccountTotal['account_total'];
=======
// if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
     // if (empty($_POST['user_name'])) {
     //      die("Location: index.php?error= User Name is required");
     //      // echo "Location: index.php?error= User Name is required";
     //    }
        
     //    if (empty($_POST['password'])) {
     //      die("Location: index.php?error= Password is required");
     //    }
        
     //    if (empty($_POST['password_repeat'])) {
     //      die("Location: index.php?error= Password Repeat is required");
     //    }
        
     //    if (empty($_POST['name'])) {
     //      die("Location: index.php?error= First Name on Account is required");
     //    }
        
        // if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
        //     die("password must have a letter");
        // }
        
     //    if ($_POST["password"] !== $_POST["password_repeat"]) {
     //      die("passwords do not match. try again");
     //    }
        
     session_start();
     include "db_conn.php";
     
     if (isset($_POST['submit'])) {
     
       $user_name = $_POST['user_name'];
     
       $pass = $_POST['password'];
     
       $name = $_POST['name'];
     
     
       $sql = "INSERT INTO `users`(`user_name`, `password`, `name`) VALUES ('$user_name','$pass','$name')";
     
       $result = $conn->query($sql);
     
       if ($result == TRUE) {
     //   if ($result != TRUE) {
     
         $name = $_POST["name"];
         echo"Hello, ". $name ."<br>";
     //     echo $_SESSION['name'];
       } else {
     
         echo "Error:" . $sql . "<br>" . $conn->error;
       }
     
       $conn->close();
     }
     if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
>>>>>>> c60cd1a24a893bb2669595a64eef45801d63fb69

 ?>
<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<<<<<<< HEAD
     <h1>Account For <?php echo $_SESSION['name']?> </h1>
     <br></br>
     <h2> Account Total <?php echo $accountTotal; ?></h2>
     <table border='1'>
          <tr>
               <th>Transaction ID</th>
               <th>Date</th>
               <th>Amount</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
               <td><?php echo $row['transactionID']; ?></td>
               <td><?php echo $row['date']; ?></td>
               <td><?php echo $row['amount']; ?></td>
          </tr>
          <?php } ?>
     </table>
     <br></br>
     <form action="process_transaction.php" method="post">
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" required>
    <br>

    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required>
    <br>

    <!-- Assuming you have a session variable for the user ID -->
    <input type="hidden" name="account_id" value="<?php echo $_SESSION['id']; ?>">
    <input type= "hidden" name="account_total" value="<?php echo $accountTotal; ?>">

    <input type="submit" value="Submit">
</form>
<?php
if (isset($_GET['success'])) {
    $successMessage = $_GET['success'];
    echo "<div style='color: green;'>$successMessage</div>";
}
?>

     <a href="logout.php">Logout</a>
=======
     <h1>Hello, <?php echo $_SESSION['name']; ?></h1>  
     <!-- <a href="logout.php">Logout</a> -->
     <a href="index.php">Logout</a>
>>>>>>> c60cd1a24a893bb2669595a64eef45801d63fb69
</body>
</html>

<?php 
    // Close the statements and connection
    $stmt->close();
    $stmtAccountTotal->close();
    $conn->close();
}else{
     header("Location: index.php");
     exit();
}
?>
