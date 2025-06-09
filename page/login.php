<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$message = '';

// Display session message if exists
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = 'Please enter both username and password.';
    } else {
        try {
            // Fetch user data
            $stmt = $pdo->prepare("SELECT id, username, password, status FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user) {
                // Check if account is inactive
                if ($user['status'] === 'inactive') {
                    $message = 'Your account has been deactivated. Please contact support.';
                }
                // Verify password
                elseif (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header('Location: main.php');
                    exit();
                } else {
                    $message = 'Invalid username or password.';
                }
            } else {
                $message = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <!-- Display feedback message -->
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <!-- Links for account help -->
        <p><a href="forgot_password.php">Forgot password?</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
