<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? 0;

    // Validate input amount
    if (!is_numeric($amount) || $amount <= 0) {
        $message = 'Invalid amount. Please enter a positive number.';
        $message_type = 'error';
    } elseif ($amount > 20000) { // Max withdrawal limit
        $message = 'Withdrawal amount exceeds the maximum limit of $20,000.';
        $message_type = 'error';
    } else {
        try {
            // Start DB transaction
            $pdo->beginTransaction();

            // Lock user's balance row for update
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id FOR UPDATE");
            $stmt->execute(['user_id' => $user_id]);
            $current_balance = $stmt->fetchColumn();

            if ($current_balance === false) {
                $pdo->rollBack();
                $message = 'User not found.';
                $message_type = 'error';
            } elseif ($current_balance < $amount) { // Check sufficient balance
                $pdo->rollBack();
                $message = 'Insufficient balance. Your current balance is $' . number_format($current_balance, 2);
                $message_type = 'error';
            } else {
                $new_balance = $current_balance - $amount;

                // Update user balance
                $stmt = $pdo->prepare("UPDATE users SET balance = :new_balance WHERE id = :user_id");
                $stmt->execute([
                    'new_balance' => $new_balance,
                    'user_id' => $user_id
                ]);

                // Insert withdrawal transaction record
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, current_balance, timestamp) VALUES (:user_id, :type, :amount, :current_balance, NOW())");
                $stmt->execute([
                    'user_id' => $user_id,
                    'type' => 'withdraw',
                    'amount' => $amount,
                    'current_balance' => $new_balance
                ]);

                // Commit transaction
                $pdo->commit();
                $message = 'Withdrawal successful! Your new balance is $' . number_format($new_balance, 2);
                $message_type = 'success';
            }

        } catch (PDOException $e) {
            // Rollback on error
            $pdo->rollBack();
            $message = 'Transaction failed: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Withdraw</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <div class="container">
        <h2>Withdraw Money</h2>
        <?php if ($message): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="withdraw.php" method="POST">
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required />
            </div>
            <button type="submit">Withdraw</button>
        </form>
        <p><a href="main.php">Back to Main Menu</a></p>
    </div>
</body>
</html>
