<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Redirect unauthenticated users to login page
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

    // Validate deposit amount input
    if (!is_numeric($amount) || $amount <= 0) {
        $message = 'Invalid amount. Please enter a positive number.';
        $message_type = 'error';
    } elseif ($amount > 100000) {
        $message = 'Deposit amount exceeds the maximum limit of $100,000.';
        $message_type = 'error';
    } else {
        try {
            // Begin transaction to ensure atomicity
            $pdo->beginTransaction();

            // Lock user's balance row to prevent concurrent modifications
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id FOR UPDATE");
            $stmt->execute(['user_id' => $user_id]);
            $current_balance = $stmt->fetchColumn();

            if ($current_balance === false) {
                throw new Exception('User not found or balance could not be retrieved.');
            }

            // Calculate new balance after deposit
            $new_balance = $current_balance + $amount;

            // Update user's balance with new value
            $stmt = $pdo->prepare("UPDATE users SET balance = :new_balance WHERE id = :user_id");
            $stmt->execute([
                'new_balance' => $new_balance,
                'user_id' => $user_id
            ]);

            // Insert a transaction record for this deposit
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, current_balance) VALUES (:user_id, :type, :amount, :current_balance)");
            $stmt->execute([
                'user_id' => $user_id,
                'type' => 'deposit',
                'amount' => $amount,
                'current_balance' => $new_balance
            ]);

            // Commit transaction to finalize changes
            $pdo->commit();
            $message = 'Deposit successful! Your new balance is $' . number_format($new_balance, 2);
            $message_type = 'success';

        } catch (PDOException $e) {
            // Roll back transaction on database error
            $pdo->rollBack();
            $message = 'Transaction failed: ' . $e->getMessage();
            $message_type = 'error';
        } catch (Exception $e) {
            // Roll back transaction on general error
            $pdo->rollBack();
            $message = 'Error: ' . $e->getMessage();
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
    <title>Deposit</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <div class="container">
        <h2>Deposit Money</h2>
        <?php if ($message): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="deposit.php" method="POST">
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required />
            </div>
            <button type="submit">Deposit</button>
        </form>
        <p><a href="main.php">Back to Main Menu</a></p>
    </div>
</body>
</html>
