<?php
require_once '../config.php';
requireLogin();

$success = '';
$error = '';

// Create settings table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hide_admin_link = isset($_POST['hide_admin_link']) ? '1' : '0';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('hide_admin_link', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$hide_admin_link, $hide_admin_link]);
        $success = 'Settings saved successfully!';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get current settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'hide_admin_link'");
$stmt->execute();
$current_setting = $stmt->fetchColumn();
$hide_admin_link = $current_setting === '1';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>Website Settings</h1>
            <p style="color: #666; margin-bottom: 30px;">Configure your website options</p>

            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 600px;">
                <form method="POST">
                    <h3 style="margin-bottom: 20px;">Navigation Settings</h3>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="hide_admin_link" value="1" <?= $hide_admin_link ? 'checked' : '' ?> style="width: auto; cursor: pointer;">
                            <div>
                                <strong>Hide Admin Login Link from Public Navigation</strong><br>
                                <small style="color: #666;">When enabled, the "Admin" link will be hidden from the public website navigation. You can still access admin panel via direct URL: /login.php</small>
                            </div>
                        </label>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>

                <div style="margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px;">
                    <h4 style="margin-bottom: 10px; color: #333;">💡 Admin Access URLs:</h4>
                    <p style="font-size: 14px; color: #666; line-height: 1.8;">
                        <strong>Admin Login:</strong> <code style="background: #e5e7eb; padding: 2px 8px; border-radius: 4px;">yourdomain.com/login.php</code><br>
                        <strong>Admin Panel:</strong> <code style="background: #e5e7eb; padding: 2px 8px; border-radius: 4px;">yourdomain.com/admin/</code><br>
                        <small>These URLs will always work, regardless of visibility settings.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>