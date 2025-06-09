<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get username from session or use 'Guest' as default
$username = $_SESSION['username'] ?? 'Guest';
?>

<div class="header">
    <div class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </div>
    <div class="app-title">ATM System</div>
    <div class="user-info">
        <span>Hello, <?php echo htmlspecialchars($username); ?></span>
        <div class="settings-dropdown" id="settingsDropdown">
            <i class="fas fa-cog cog-icon"></i>
            <div class="dropdown-content">
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        Welcome, <?php echo htmlspecialchars($username); ?>
    </div>
    <ul class="sidebar-nav">
        <!-- Highlight current page -->
        <li><a href="main.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="deposit.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'deposit.php') ? 'active' : ''; ?>"><i class="fas fa-money-bill-wave"></i> Deposit</a></li>
        <li><a href="withdraw.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'withdraw.php') ? 'active' : ''; ?>"><i class="fas fa-hand-holding-usd"></i> Withdraw</a></li>
        <li><a href="transactions.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'active' : ''; ?>"><i class="fas fa-history"></i> Transaction History</a></li>
        <li><a href="settings.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
