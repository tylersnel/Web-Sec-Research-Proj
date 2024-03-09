<?php
session_start();


require_once __DIR__ . '/vendor/autoload.php'; // Load dotenv library
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Function to call and print data from Yahoo Finance API
function getYahooFinanceData($symbol)
{
    $allowedDomains = array("yahoo-finance127.p.rapidapi.com"); // Define an array of allowed domains

    // Extract the domain from the URL
   $urlParts = parse_url("https://yahoo-finance127.p.rapidapi.com/price/{$symbol}");
   $domain = $urlParts['host'];

    // Check if the domain is in the whitelist
    if ($symbol) {
        $apiKey = $_ENV['RAPIDAPI_KEY']; // Access the API key from environment variable

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://yahoo-finance127.p.rapidapi.com/price/{$symbol}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: yahoo-finance127.p.rapidapi.com",
                "X-RapidAPI-Key: $apiKey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error: " . $err;
        } else {
            $data = json_decode($response, true);
            if (isset($data['regularMarketPrice']['raw'])) {
                $symbol = $data['symbol'];
                $price = $data['regularMarketPrice']['raw'];
                echo "Stock Symbol: {$symbol}, Price: {$price}";
            } else {
                echo "Unable to fetch data for {$symbol}";
                // Uncomment the line below for debugging
                // var_dump($data); // Print the response for debugging purposes
            }
        }
    } else {
        echo "Unauthorized domain: {$domain}";
    }
}



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
        <br>
        <br>
        <ul>
            <li><a href="transfer_funds.php">Transfer Funds</a></li>
            <li><a href="bill_pay.php">Pay Bills</a></li>
        </ul>
        <br>
        <br>
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

            <label for="transactionType">Select Transaction Type:</label>
            <select id="transactionType" name="transactionType">
                <option value="deposit">Deposit</option>
                <option value="withdrawal">Withdrawal</option>
            </select>
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

        $errors = array();
        if ($_SESSION['account_total'] < 0) {
            array_push($errors, "Overdrawn Account!");
            if (count($errors) > 0) {
                foreach ($errors as  $error) {
                    echo "<div class='error' style='color: red;'>$error</div>";
                }
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="symbol">Enter Stock Symbol:</label>
            <input type="text" id="symbol" name="symbol" required>
            <input type="submit" value="Get Yahoo Finance Data">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $symbol = $_POST['symbol'];
            getYahooFinanceData($symbol);
        }
        ?>

        <br></br>
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
