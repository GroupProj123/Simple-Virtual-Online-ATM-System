<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$message = '';
$message_type = '';
$current_stage = 'verify_identity'; // Default stage: verify identity

// If identity already verified, move to password reset stage
if (isset($_SESSION['reset_user_id']) && isset($_SESSION['reset_username'])) {
    $current_stage = 'set_new_password';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($current_stage === 'verify_identity') {
        // Stage 1: Identity verification
        $username = trim($_POST['username'] ?? '');
        $birthday = $_POST['birthday'] ?? '';

        if (empty($username) || empty($birthday)) {
            $message = 'Username and Birthday are required for verification.';
            $message_type = 'error';
        } else {
            try {
                // Query user by username
                $stmt = $pdo->prepare("SELECT id, birthday, status FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();

                if (!$user) {
                    $message = 'Username not found. Please check your username.';
                    $message_type = 'error';
                } elseif ($user['status'] !== 'active') {
                    $message = 'Your account is inactive. Please contact support.';
                    $message_type = 'error';
                } elseif (empty($user['birthday'])) {
                    $message = 'Cannot reset password via birthday since birthday is not set.';
                    $message_type = 'error';
                } elseif ($user['birthday'] !== $birthday) {
                    $message = 'Incorrect birthday. Please ensure it matches your registered birthday.';
                    $message_type = 'error';
                } else {
                    // Identity verified successfully, proceed to next stage
                    $_SESSION['reset_user_id'] = $user['id'];
                    $_SESSION['reset_username'] = $username;
                    $message = 'Identity verified! You can now set your new password.';
                    $message_type = 'success';
                    $current_stage = 'set_new_password';
                }
            } catch (PDOException $e) {
                $message = 'Verification failed: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    } elseif ($current_stage === 'set_new_password') {
        // Stage 2: Set new password
        if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['reset_username'])) {
            // Session expired or invalid, reset to stage 1
            $message = 'Session expired. Please start the password reset again.';
            $message_type = 'error';
            $current_stage = 'verify_identity';
            unset($_SESSION['reset_user_id'], $_SESSION['reset_username']);
        } else {
            $user_id = $_SESSION['reset_user_id'];
            $new_password = $_POST['new_password'] ?? '';
            $confirm_new_password = $_POST['confirm_new_password'] ?? '';

            if (empty($new_password) || empty($confirm_new_password)) {
                $message = 'New password and confirmation are required.';
                $message_type = 'error';
            } elseif ($new_password !== $confirm_new_password) {
                $message = 'New passwords do not match.';
                $message_type = 'error';
            } elseif (strlen($new_password) < 6) {
                $message = 'New password must be at least 6 characters long.';
                $message_type = 'error';
            } else {
                try {
                    // Check current password to prevent reuse
                    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
                    $stmt->execute(['user_id' => $user_id]);
                    $user_data = $stmt->fetch();

                    if (!$user_data) {
                        $message = 'User data not found. Please try again.';
                        $message_type = 'error';
                        $current_stage = 'verify_identity';
                        unset($_SESSION['reset_user_id'], $_SESSION['reset_username']);
                    } elseif (password_verify($new_password, $user_data['password'])) {
                        $message = 'New password cannot be the same as the old password.';
                        $message_type = 'error';
                    } else {
                        // Update password securely
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                        $stmt->execute([
                            'password' => $hashed_password,
                            'user_id' => $user_id
                        ]);

                        // Clear session and redirect to login
                        unset($_SESSION['reset_user_id'], $_SESSION['reset_username']);
                        $_SESSION['message'] = 'Password reset successful! Please log in with your new password.';
                        $_SESSION['message_type'] = 'success';
                        header('Location: login.php');
                        exit;
                    }
                } catch (PDOException $e) {
                    $message = 'Password update failed: ' . $e->getMessage();
                    $message_type = 'error';
                }
            }
        }
    }
}
?>
