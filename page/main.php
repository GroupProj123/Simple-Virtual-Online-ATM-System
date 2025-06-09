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
$balance = 0.00;

try {
    // Get user balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $balance = $stmt->fetchColumn() ?: 0.00;

    // Get user birthday to determine if popup needed
    $stmt = $pdo->prepare("SELECT birthday FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $birthday = $stmt->fetchColumn();

    $show_birthday_popup = empty($birthday); // Show popup if birthday not set

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ATM Main Menu</title>
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <?php include __DIR__ . '/../includes/header_sidebar.php'; // Shared header/sidebar ?>

    <div class="content" id="mainContent">
        <div class="main-dashboard-card">
            <h1 class="welcome-message">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <h3>Your Current Balance:</h3>
            <p id="balanceDisplay">
                $<span id="actualBalance" class="hidden"><?php echo number_format($balance, 2); ?></span>
                <span id="maskedBalance" class="visible-inline">*****.**</span>
                <i class="fas fa-eye toggle-balance" id="toggleBalance"></i>
            </p>
        </div>
    </div>

    <?php if ($show_birthday_popup): ?>
    <div class="overlay" id="overlay"></div>
    <div class="birthday-popup" id="birthdayPopup">
        <h3>Please set up your birthday!</h3>
        <p>Set your birthday so you can recover your password if forgotten!</p>
        <button id="goToSettings">Go to settings</button>
        <button id="closeBirthdayPopup">Maybe later</button>
    </div>
    <?php endif; ?>

    <script src="../js/main.js"></script>
</body>
</html>
