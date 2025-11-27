<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken';
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                // Create user account (role = user, not admin)
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, full_name, password, role) VALUES (?, ?, ?, ?, 'user')");
                $stmt->execute([$username, $email, $full_name, $hashed_password]);
                
                $success = 'Account created successfully! You can now login.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - GamingToday</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 16px; max-width: 450px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { text-align: center; color: #333; margin-bottom: 10px; font-size: 32px; }
        h1 span { color: #667eea; }
        p { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 14px; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: #667eea; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 10px; }
        button:hover { opacity: 0.9; }
        .error { background: #fee; border: 1px solid #fcc; padding: 10px; border-radius: 8px; color: #c00; margin-bottom: 20px; text-align: center; }
        .success { background: #efe; border: 1px solid #cfc; padding: 10px; border-radius: 8px; color: #060; margin-bottom: 20px; text-align: center; }
        .links { text-align: center; margin-top: 20px; font-size: 14px; }
        .links a { color: #667eea; text-decoration: none; font-weight: 600; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gaming<span>Today</span></h1>
        <p>Create your account</p>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" required placeholder="John Doe" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required placeholder="Choose a username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required placeholder="your@email.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Password * (min 6 characters)</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>

            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" required placeholder="Confirm password">
            </div>

            <button type="submit">Create Account</button>
        </form>

        <div class="links">
            Already have an account? <a href="user-login.php">Login here</a><br>
            <a href="index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>