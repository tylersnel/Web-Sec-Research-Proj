<?php
session_start(); 
include "db_conn.php";


if (isset($_POST['uname']) && isset($_POST['password'])){
    $uname = $_POST['uname'];
    $pass = $_POST['password'];

    if (empty($uname)) {
		header("Location: index.php?error=User Name is required");
	    exit();
	}else if(empty($pass)){
        header("Location: index.php?error=Password is required");
	    exit();
	}else{
		$sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
            if ($row['user_name'] === $uname && $row['password'] === $pass) {
            	$_SESSION['user_name'] = $row['user_name'];
            	$_SESSION['name'] = $row['name'];
            	$_SESSION['id'] = $row['id'];
				$_SESSION['account_total'] = $row['account_total'];

				// Additional query for transaction table
				// Additional query to retrieve data from transactions table based on the account ID
                $fk_Id = $row['id'];
                $additionalQuery = "SELECT * FROM transactions WHERE accountid='$fk_Id'";
                $additionalResult = mysqli_query($conn, $additionalQuery);

                if ($additionalResult) {
                    $additionalData = mysqli_fetch_assoc($additionalResult);
                    // Process the additional data as needed
                    $_SESSION['amount'] = $additionalData['amount'];
					$_SESSION['transactionID'] = $additionalData['transactionID'];
					$_SESSION['date'] = $additionalData['date'];
                }


            	header("Location: home.php");
		        exit();
            }else{
				header("Location: index.php?error=Incorect User name or password");
		        exit();
			}
		}else{
			header("Location: index.php?error=Incorect User name or password");
	        exit();
		}
	}

}else{
    header("Location: index.php");
    exit();
}