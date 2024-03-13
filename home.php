<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


require_once __DIR__ . '/vendor/autoload.php'; // Load dotenv library
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Function to call and print data from Yahoo Finance API
function getYahooFinanceData($symbol)
{
    $allowedDomains = array("yahoo-finance127.p.rapidapi.com"); // Define an array of allowed domains
    // $allowedDomains = array("");

    // Extract the domain from the URL
    $urlParts = parse_url("https://yahoo-finance127.p.rapidapi.com/price/{$symbol}");
    // $urlParts = parse_url($symbol);
    $domain = $urlParts['host'];

    // Check if the domain is in the whitelist
    if (in_array($domain, $allowedDomains)) {
        // if (($domain)) {
        $apiKey = $_ENV['RAPIDAPI_KEY']; // Access the API key from environment variable

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://yahoo-finance127.p.rapidapi.com/price/{$symbol}",
            // CURLOPT_URL => $symbol,
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>

    <body>
        <div class="container border bg-light shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                </symbol>
            </svg>
            <h1>Account For <?php echo $_SESSION['name'] ?> </h1>
            <br>
            <ul>
                <li><button class="text-primary my-1"><a href="transfer_funds.php">Transfer Funds</a></button></li>
                <li><button class="text-primary my-1"><a href="bill_pay.php">Pay Bills</a></button></li>
            </ul>
            <br>
            <h2> Account Total <?php echo $accountTotal; ?></h2>

            <table class="table table-striped table-bordered table-hover table-sm">
                <caption>List of Transactions</caption>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Transaction ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <th scope="row"><?php echo $row['transactionID']; ?></th>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
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

                <input class="btn btn-primary my-1" type="submit" value="Submit">

            </form>

            <?php
            if (isset($_GET['success'])) {
                $successMessage = $_GET['success'];
                echo    "<div class='text-center alert alert-success my-1 alert-dismissable fade show' role='alert' style='margin-top: -5px;'>
                            <strong class='my-5'>
                                $successMessage
                            </strong>
                            <div style='text-align: right; margin-top: -30px;'>
                                <button type='button' class='btn-close text-end' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        </div>";
            }

            $errors = array();
            if ($_SESSION['account_total'] < 0) {
                array_push($errors, "Overdrawn Account!");
                if (count($errors) > 0) {
                    foreach ($errors as  $error) {
                        echo    "<div class='alert alert-danger d-flex align-items-center mx-auto' role='alert'>
                                    <svg class='bi flex-shrink-0 me-1' style='width: 24px; height: 24px;' role='img' aria-label='Danger:'><use xlink:href='#exclamation-triangle-fill'/></svg>
                                        <div>
                                            $error
                                        </div>
                                </div>";
                    }
                }
            }
            ?>

            <br></br>

            <form id="stockForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="symbol">Enter Stock Symbol:</label>
                <input type="text" id="symbol" name="symbol" required>
                <input id="submitButton" class="btn btn-primary my-1" type="submit" value="Get Yahoo Finance Data">
                <button id="spinnerButton" class="btn btn-primary visually-hidden" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span class="visually-hidden">Loading...</span>
                </button>
            </form>



            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $symbol = $_POST['symbol'];
                getYahooFinanceData($symbol);
            }
            ?>

            <br></br>
            <button class="text-primary"><a href="logout.php">Logout</a></button>
            <!-- <a href="logout.php">Logout</a> -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
            <script>
                document.getElementById("stockForm").addEventListener("submit", function(event) {
                    // Show spinner button
                    document.getElementById("spinnerButton").classList.remove("visually-hidden");
                    document.getElementById("submitButton").classList.add("visually-hidden");
                });
            </script>
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