<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';
    $birthday_input = $_POST['birthday'] ?? '';

    $password_change_requested = !empty($new_password);
    $birthday_set_requested = !empty($birthday_input);

    try {
        // Fetch user password and birthday
        $stmt = $pdo->prepare("SELECT password, birthday FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user_data = $stmt->fetch();

        if (!$user_data) {
            $message = "User not found. Please log in again.";
            $message_type = "error";
        } elseif (empty($current_password)) {
            $message = "Please enter your current password to update information.";
            $message_type = "error";
        } elseif (!password_verify($current_password, $user_data['password'])) {
            $message = "Current password is incorrect.";
            $message_type = "error";
        } else {
            $has_changes = false;

            // Update password if requested and valid
            if ($password_change_requested) {
                if ($new_password !== $confirm_new_password) {
                    $message = "New passwords do not match.";
                    $message_type = "error";
                } elseif (strlen($new_password) < 6) {
                    $message = "New password must be at least 6 characters long.";
                    $message_type = "error";
                } elseif (password_verify($new_password, $user_data['password'])) {
                    $message = "New password cannot be the same as the old password.";
                    $message_type = "error";
                } else {
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                    $stmt->execute(['password' => $hashed_new_password, 'user_id' => $user_id]);
                    $message = "Password updated successfully!";
                    $message_type = "success";
                    $has_changes = true;
                }
            }

            // Update birthday only if not set yet and valid format
            $existing_birthday = $user_data['birthday'];
            if ($birthday_set_requested) {
                if (empty($existing_birthday)) {
                    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $birthday_input)) {
                        $stmt = $pdo->prepare("UPDATE users SET birthday = :birthday WHERE id = :user_id");
                        $stmt->execute(['birthday' => $birthday_input, 'user_id' => $user_id]);
                        $birthday_message = "Birthday set successfully!";
                        if (empty($message)) {
                            $message = $birthday_message;
                            $message_type = "success";
                        } else {
                            $message .= " And " . $birthday_message;
                            if ($message_type !== "error") $message_type = "success";
                        }
                        $has_changes = true;
                    } else {
                        $birthday_error_message = "Invalid birthday format.";
                        if (empty($message)) {
                            $message = $birthday_error_message;
                            $message_type = "error";
                        } else {
                            $message .= " But " . $birthday_error_message;
                            $message_type = "error";
                        }
                    }
                } else {
                    $birthday_warning_message = "Birthday cannot be changed once set.";
                    if (empty($message)) {
                        $message = $birthday_warning_message;
                        $message_type = "warning";
                    } else {
                        $message .= ". " . $birthday_warning_message;
                        if ($message_type === "success") $message_type = "warning";
                    }
                }
            }

            // If no changes detected
            if (!$has_changes && empty($message)) {
                $message = "No new information to update or no changes detected.";
                $message_type = "info";
            }
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_type = "error";
    }
} else {
    // Redirect to settings if accessed directly
    header('Location: settings.php');
    exit;
}

// Save message and type in session and redirect back
$_SESSION['message'] = $message;
$_SESSION['message_type'] = $message_type;
header('Location: settings.php');
exit;
?>
