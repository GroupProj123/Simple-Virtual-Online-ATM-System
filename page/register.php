<?php
require_once __DIR__ . '/../config/db.php';

$message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $message = 'Username already taken.';
            } else {
                // Insert new user with hashed password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, balance, status) VALUES (:username, :password, :balance, :status)");
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashed_password,
                    'balance' => 0.00,
                    'status' => 'active'
                ]);
                $message = 'Registration successful! You can now <a href="login.php">login</a>.';
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
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>

        <!-- Display feedback message -->
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Registration form -->
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Register</button>
        </form>

        <!-- Link to login page -->
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
