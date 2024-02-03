<?php
session_start();
include "db_conn.php"; // Include your database connection script

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $amount = $_POST['amount'];
    $transactionDate = $_POST['date'];
    $accountId = $_POST['account_id'];
    $account_total = $_POST['account_total'];
    $transaction_type=$_POST['transactionType'];

    // Validate form data (you can add more validation as needed)
    if (empty($amount) || empty($transactionDate) || empty($accountId)) {
        header("Location: transaction_form.php?error=All fields are required");
        exit();
    }
    //If withdrawling from bank account
    if($transaction_type == 'withdrawal'){
        $amount*=-1;
    }

    // SQL to insert the transaction into the 'transactions' table
    $insertQuery = "INSERT INTO transactions (amount, date, accountid) 
                    VALUES ('$amount', '$transactionDate', '$accountId')";

    if (mysqli_query($conn, $insertQuery)) {
        // Redirect to the transaction form with a success message

        $account_total = $account_total + $amount;
        $updateQuery = "UPDATE users 
                       SET account_total=?
                       WHERE id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($updateQuery);

        // Bind parameters
        $stmt->bind_param("ii", $account_total, $accountId);

        // Execute the statement
        if ($stmt->execute()) {
            // Successful update
            // Update the session variable for account_total
            $_SESSION['account_total'] = $account_total;
            
            // Redirect to the transaction form with a success message
            header("Location: home.php?success=Transaction added successfully");
            exit();
        } else {
            // Error during update
            header("Location: home.php?error=Error adding transaction. Please try again.");
            exit();
        }
    } else {
        // Redirect to the transaction form with an error message
        header("Location: home.php?error=Error adding transaction. Please try again.");
        exit();
    }
}

// Close the database connection if needed
