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
$birthday = null;

$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

try {
    // Fetch birthday and status from DB
    $stmt = $pdo->prepare("SELECT birthday, status FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user_data = $stmt->fetch();

    if ($user_data) {
        $birthday = $user_data['birthday'];
        $account_status = $user_data['status'];
    } else {
        // User not found, logout
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Account Settings</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        /* Modal styles */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000;
            visibility: hidden; opacity: 0;
            transition: visibility 0s, opacity 0.3s ease-in-out;
        }
        .modal-overlay.active {
            visibility: visible; opacity: 1;
        }
        .modal-content {
            background: white; padding: 30px; border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 90%; max-width: 500px;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.3s ease-in-out;
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }
        /* Buttons and messages styling omitted for brevity */
    </style>
</head>
<body>
    <div class="container">
        <h2>Account Settings</h2>

        <?php if ($message): ?>
            <p class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <div class="setting-item">
            <label>Username:</label>
            <span><?php echo htmlspecialchars($username); ?></span>
        </div>

        <form action="update_info.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required />
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" />
            </div>
            <div class="form-group">
                <label for="confirm_new_password">Confirm New Password:</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" />
            </div>

            <?php if (empty($birthday)): ?>
            <div class="form-group">
                <label for="birthday">Birthday:</label>
                <input type="date" id="birthday" name="birthday" />
                <small>Once set, birthday cannot be changed.</small>
            </div>
            <?php else: ?>
            <div class="setting-item">
                <label>Birthday:</label>
                <span><?php echo htmlspecialchars($birthday); ?></span>
                <small>(Birthday cannot be changed once set)</small>
            </div>
            <?php endif; ?>

            <button type="submit">Update Information</button>
        </form>

        <button type="button" class="danger-button" id="deactivateAccountBtn">Deactivate Account</button>

        <p><a href="main.php">Back to Main Menu</a></p>
    </div>

    <div id="deactivateModal" class="modal-overlay">
        <div class="modal-content">
            <h3>Confirm Account Deactivation</h3>
            <p><strong>This action is irreversible!</strong> Are you sure?</p>
            <p>Enter your username, current password, and type "DEACTIVATE" below:</p>
            <form action="delete.php" method="POST">
                <input type="hidden" name="action" value="deactivate_account" />
                <div class="form-group">
                    <label for="username_confirm">Username:</label>
                    <input type="text" id="username_confirm" name="username_confirm" required autocomplete="username" />
                </div>
                <div class="form-group">
                    <label for="current_password_confirm">Current Password:</label>
                    <input type="password" id="current_password_confirm" name="current_password_confirm" required autocomplete="current-password" />
                </div>
                <div class="form-group">
                    <label for="confirm_text">Type "DEACTIVATE" to confirm:</label>
                    <input type="text" id="confirm_text" name="confirm_text" required placeholder="DEACTIVATE" />
                </div>
                <div class="button-group">
                    <button type="submit" class="danger-button">Confirm Deactivation</button>
                    <button type="button" id="cancelDeactivateBtn" class="button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal open/close logic
        document.addEventListener('DOMContentLoaded', function () {
            const deactivateAccountBtn = document.getElementById('deactivateAccountBtn');
            const deactivateModal = document.getElementById('deactivateModal');
            const cancelDeactivateBtn = document.getElementById('cancelDeactivateBtn');

            deactivateAccountBtn.addEventListener('click', () => {
                deactivateModal.classList.add('active');
            });

            cancelDeactivateBtn.addEventListener('click', () => {
                deactivateModal.classList.remove('active');
                deactivateModal.querySelector('form').reset();
            });

            deactivateModal.addEventListener('click', (event) => {
                if (event.target === deactivateModal) {
                    deactivateModal.classList.remove('active');
                    deactivateModal.querySelector('form').reset();
                }
            });
        });
    </script>
</body>
</html>
