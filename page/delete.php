<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deactivate_account') {
    $username_input = trim($_POST['username_confirm'] ?? '');
    $current_password_input = $_POST['current_password_confirm'] ?? '';
    $confirm_text_input = trim($_POST['confirm_text'] ?? '');

    try {
        // Fetch current user's data from database
        $stmt = $pdo->prepare("SELECT username, password FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user_data = $stmt->fetch();

        if (!$user_data) {
            $message = 'User data not found.';
            $message_type = 'error';
        } elseif ($username_input !== $user_data['username']) {
            $message = 'Incorrect username. Please try again.';
            $message_type = 'error';
        } elseif (!password_verify($current_password_input, $user_data['password'])) {
            $message = 'Incorrect password. Please try again.';
            $message_type = 'error';
        } elseif (strtoupper($confirm_text_input) !== 'DEACTIVATE') {
            // Confirm user typed "DEACTIVATE"
            $message = 'You must type "DEACTIVATE" to confirm.';
            $message_type = 'error';
        } else {
            // All validations passed: deactivate account
            $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            // Clear session and redirect to login page
            session_unset();
            session_destroy();
            $_SESSION['message'] = 'Your account has been successfully deactivated. You can no longer log in.';
            $_SESSION['message_type'] = 'success';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        $message = 'Account deactivation failed: ' . $e->getMessage();
        $message_type = 'error';
    }

    // Save error message in session and redirect back to settings page
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $message_type;
    header('Location: settings.php');
    exit;
}

// Prevent direct access without POST request or proper action
header('Location: settings.php');
exit;
?>
