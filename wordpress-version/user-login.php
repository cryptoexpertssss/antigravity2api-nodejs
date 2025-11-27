<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] === 'admin') {
            redirect('admin/index.php');
        } else {
            redirect('user-dashboard.php');
        }
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - GamingToday</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 16px; max-width: 400px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { text-align: center; color: #333; margin-bottom: 10px; font-size: 32px; }
        h1 span { color: #667eea; }
        p { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: #667eea; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 10px; }
        button:hover { opacity: 0.9; }
        .error { background: #fee; border: 1px solid #fcc; padding: 10px; border-radius: 8px; color: #c00; margin-bottom: 20px; text-align: center; }
        .links { text-align: center; margin-top: 20px; font-size: 14px; }
        .links a { color: #667eea; text-decoration: none; font-weight: 600; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gaming<span>Today</span></h1>
        <p>Login to your account</p>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="links">
            Don't have an account? <a href="register.php">Sign up here</a><br>
            <a href="index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>