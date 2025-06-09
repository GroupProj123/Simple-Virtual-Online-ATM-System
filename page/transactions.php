<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$transactions = [];

try {
    // Fetch all transactions for current user, newest first
    $stmt = $pdo->prepare("SELECT type, amount, current_balance, timestamp FROM transactions WHERE user_id = :user_id ORDER BY timestamp DESC");
    $stmt->execute(['user_id' => $user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle DB errors
    die("Error fetching transactions: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Transaction History</title>
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="../css/transactions.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <?php include __DIR__ . '/../includes/header_sidebar.php'; // Include common header and sidebar ?>

    <div class="content" id="mainContent">
        <div class="container" style="max-width: 800px;">
            <h2>Transaction History</h2>

            <?php if (empty($transactions)): ?>
                <p>No transactions found yet.</p>
            <?php else: ?>
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance After</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="<?php echo ($transaction['type'] === 'deposit') ? 'deposit-row' : 'withdraw-row'; ?>">
                                <td data-label="Type:"><?php echo ucfirst($transaction['type']); ?></td>
                                <td data-label="Amount:" class="amount-<?php echo $transaction['type']; ?>">
                                    <?php
                                        if ($transaction['type'] === 'withdraw') {
                                            echo '-$' . number_format($transaction['amount'], 2);
                                        } else {
                                            echo '$' . number_format($transaction['amount'], 2);
                                        }
                                    ?>
                                </td>
                                <td data-label="Balance After:">$<?php echo number_format($transaction['current_balance'], 2); ?></td>
                                <td data-label="Date:"><?php echo date('Y-m-d H:i:s', strtotime($transaction['timestamp'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
