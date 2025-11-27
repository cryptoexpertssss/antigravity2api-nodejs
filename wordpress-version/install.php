<?php
require_once 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create tables
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'editor') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            content TEXT NOT NULL,
            excerpt TEXT,
            category_id INT,
            author VARCHAR(100),
            featured_image VARCHAR(255),
            status ENUM('draft', 'published') DEFAULT 'published',
            meta_description TEXT,
            tags TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS casinos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            rank INT NOT NULL,
            logo_url VARCHAR(255),
            images TEXT,
            offer_title VARCHAR(255),
            offer_details TEXT,
            features TEXT,
            promo_code VARCHAR(50),
            claim_link VARCHAR(255),
            rating DECIMAL(2,1) DEFAULT 5.0,
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            casino_id INT NOT NULL,
            user_name VARCHAR(100) NOT NULL,
            user_avatar VARCHAR(255),
            rating DECIMAL(2,1) NOT NULL,
            title VARCHAR(255) NOT NULL,
            comment TEXT NOT NULL,
            pros TEXT,
            cons TEXT,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (casino_id) REFERENCES casinos(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS affiliate_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            casino_id INT,
            url VARCHAR(255) NOT NULL,
            description TEXT,
            clicks INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (casino_id) REFERENCES casinos(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS advertisements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            position ENUM('header', 'sidebar', 'footer', 'in-content') NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            link_url VARCHAR(255) NOT NULL,
            alt_text VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            impressions INT DEFAULT 0,
            clicks INT DEFAULT 0,
            start_date DATE,
            end_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";

        $pdo->exec($sql);

        // Create default admin user
        $admin_username = sanitize($_POST['admin_username']);
        $admin_email = sanitize($_POST['admin_email']);
        $admin_password = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
        $admin_name = sanitize($_POST['admin_name']);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->execute([$admin_username, $admin_email, $admin_password, $admin_name]);

        $success = true;
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Install - GamingToday CMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; padding: 40px; border-radius: 16px; max-width: 500px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #333; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; }
        button:hover { opacity: 0.9; }
        .error { background: #fee; border: 1px solid #fcc; padding: 10px; border-radius: 8px; color: #c00; margin-bottom: 20px; }
        .success { background: #efe; border: 1px solid #cfc; padding: 10px; border-radius: 8px; color: #060; margin-bottom: 20px; }
        .note { background: #fef9e7; border: 1px solid #f9e79f; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎰 GamingToday CMS</h1>
        <p>Installation Setup</p>

        <?php if ($success): ?>
            <div class="success">
                ✅ Installation successful! <a href="login.php">Login now</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    ❌ <?= htmlspecialchars($error) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <div class="note">
                <strong>⚠️ Before installation:</strong><br>
                1. Create a MySQL database<br>
                2. Update config.php with database details<br>
                3. Make sure uploads/ folder is writable
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="admin_username" required placeholder="admin">
                </div>

                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" name="admin_email" required placeholder="admin@yourdomain.com">
                </div>

                <div class="form-group">
                    <label>Admin Full Name</label>
                    <input type="text" name="admin_name" required placeholder="Administrator">
                </div>

                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="password" name="admin_password" required placeholder="Strong password">
                </div>

                <button type="submit">Install Now</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>