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
          <title>Transfer Funds</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     </head>

     <body>
          <div class="container border bg-light shadow-lg">
          <h1>Transfer Funds from <?php echo $_SESSION['name'] ?>'s Account</h1>

          <h2> Account Total <?php echo $accountTotal; ?></h2>
                
          <form action="process_transaction.php" method="post">
               <label for="amount">Amount:</label>
               <input type="number" id="amount" name="amount" required>
               <br>

               <label for="date">Arrival Date:</label>
               <input type="date" id="date" name="date" required>
               <br> 

               <label for="amount_to">Receiving Account Number:</label>
               <input type="number" id="amount_to" name="amount_To" required>
               <br> 

               <!-- Assuming you have a session variable for the user ID -->
               <input type="hidden" name="account_id" value="<?php echo $_SESSION['id']; ?>">
               <input type="hidden" name="account_total" value="<?php echo $accountTotal; ?>">
               <input type="hidden" name="transactionType" value= "<?php echo 'withdrawal';?>">

               <input class="btn btn-primary my-1" type="submit" value="Submit">
          </form>
          <?php
          if (isset($_GET['success'])) {
               $successMessage = $_GET['success'];
               echo "<div style='color: green;'>$successMessage</div>";
          }
          ?>

          <br></br>
          <button class="text-primary"><a href="home.php">Return Home</a></button>
          </div>
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