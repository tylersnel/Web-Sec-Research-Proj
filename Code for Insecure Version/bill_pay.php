<?php
session_start();

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

?>
     <!DOCTYPE html>
     <html>

     <head>
          <title>Pay Bills</title>
          <link rel="stylesheet" type="text/css" href="style.css">
     </head>

     <body>
          <h1>Pay Bills for  <?php echo $_SESSION['name'] ?></h1>

          <h2> Account Total <?php echo $accountTotal; ?></h2>
                
          <form action="process_bill_transaction.php" method="post">
                <dv>From: <?php echo $_SESSION['name'] ?></dv>
                <br>
 
               <label for="amount_to">To:</label>
               <input type="text" id="amount_to" name="amount_to" required>
               <br> 

               <label for="amount">Amount:</label>
               <input type="number" id="amount" name="amount" required>
               <br>

               <label for="date">Check Date:</label>
               <input type="date" id="date" name="date" required>
               <br> 

               <label for="subject">Subject:</label>
               <input type="text" id="subject" name="subject" required>
               <br> 

               <!-- Assuming you have a session variable for the user ID -->
               <input type="hidden" name="account_id" value="<?php echo $_SESSION['id']; ?>">
               <input type="hidden" name="account_total" value="<?php echo $accountTotal; ?>">
               <input type="hidden" name="transactionType" value= "<?php echo 'withdrawal';?>">

               <input type="submit" value="Submit">
          </form>
          <?php
          if (isset($_GET['success'])) {
               $successMessage = $_GET['success'];
               echo "<div style='color: green;'>$successMessage hello there</div>";
          }
          ?>

          <br></br>
          <a href="home.php">Return Home</a>
     </body>

     </html>

<?php
     // Close the statements and connection
     $stmt->close();
     $stmtAccountTotal->close();
     $conn->close();
} else {
     header("Location: index.php");
     exit();
}
?>