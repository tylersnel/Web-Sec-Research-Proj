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
          <title>HOME</title>
          <link rel="stylesheet" type="text/css" href="style.css">
     </head>

     <body>
          <h1>Account For <?php echo $_SESSION['name'] ?> </h1>
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
               <input type="hidden" name="account_total" value="<?php echo $accountTotal; ?>">

               <input type="submit" value="Submit">
          </form>
          <?php
          if (isset($_GET['success'])) {
               $successMessage = $_GET['success'];
               echo "<div style='color: green;'>$successMessage</div>";
          }
          ?>

          <a href="logout.php">Logout</a>
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