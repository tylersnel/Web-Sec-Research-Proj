<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db_conn.php";


if (isset($_POST['user_name']) && isset($_POST['password'])) {
	$uname = $_POST['user_name'];
	$pass = $_POST['password'];

	$errors = array();

	if (empty($uname)) {
		// header("Location: index.php?error=User Name is required");
		// header("Location: index.php?<div class='error'>User Name is required</div>");
		// echo "<div class='error'>User Name is required</div>";
		// exit();
		array_push($errors, "User Name is required");
	}
	if (empty($pass)) {
		// header("Location: index.php?error=Password is required");
		// echo "<div class='error'>Password is required</div>";
		// exit();
		array_push($errors, "Password is required");
	}
	if (count($errors) > 0) {
		foreach ($errors as  $error) {
			echo "<div class='error'>$error</div>";
		}
	} else {
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
			} else {
				header("Location: index.php?error=Incorect User name or password");
				exit();
			}
		} else {
			header("Location: index.php?error=not true mysqli_num_rows(result) === 1");
			exit();
		}
	}
} else {

	header("Location: index.php");
	exit();
}
