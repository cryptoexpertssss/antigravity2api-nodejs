<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Get all casinos
$casinos = $pdo->query("SELECT * FROM casinos ORDER BY rating DESC")->fetchAll();

// Get categories for nav
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Casino Rankings - <?= SITE_NAME ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; background: #f9fafb; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; }
        .logo { font-size: 28px; font-weight: 800; color: #1a1a1a; text-decoration: none; white-space: nowrap; }
        .logo span { color: #3b82f6; }
        .nav-links { display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .nav-links a { color: #4b5563; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s; white-space: nowrap; }
        .nav-links a:hover { color: #3b82f6; }
        .auth-buttons { display: flex; gap: 15px; align-items: center; flex-shrink: 0; }
        .auth-buttons a { white-space: nowrap; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .page-header { text-align: center; margin-bottom: 50px; }
        .page-header h1 { font-size: 48px; margin-bottom: 15px; }
        .page-header p { font-size: 18px; color: #666; }
        .casino-card { background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: grid; grid-template-columns: 150px 1fr auto; gap: 30px; align-items: center; }
        .casino-logo { width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 8px; }
        .casino-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .casino-info h2 { font-size: 28px; margin-bottom: 10px; }
        .casino-rating { color: #f59e0b; font-size: 20px; margin-bottom: 10px; }
        .casino-bonus { background: #fef3c7; color: #92400e; padding: 10px 20px; border-radius: 8px; display: inline-block; margin-bottom: 15px; font-weight: 600; }
        .casino-description { color: #666; line-height: 1.8; }
        .casino-action { text-align: center; }
        .btn-play { display: inline-block; padding: 15px 40px; background: #3b82f6; color: white; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 18px; }
        .btn-play:hover { background: #2563eb; }
        .footer { background: #1f2937; color: white; padding: 40px 20px; margin-top: 60px; }
        .footer-container { max-width: 1200px; margin: 0 auto; text-align: center; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Gaming<span>Today</span></a>
            
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="casinos.php" style="color: #3b82f6;">Casinos</a>
                <?php foreach(array_slice($categories, 0, 2) as $cat): ?>
                    <a href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
            
            <div class="auth-buttons">
                <?php 
                $user_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
                if ($user_logged_in): 
                ?>
                    <a href="user-dashboard.php" style="color: #4b5563;">Dashboard</a>
                    <a href="user-logout.php" style="color: #dc2626;">Logout</a>
                <?php else: ?>
                    <a href="user-login.php" style="color: #4b5563; font-weight: 600;">Login</a>
                    <a href="register.php" style="background: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; display: inline-block; font-weight: 600; text-decoration: none;">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>🎰 Top Casino Rankings</h1>
            <p>Compare the best online casinos with verified ratings and exclusive bonuses</p>
        </div>

        <?php if(empty($casinos)): ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
                <h2>No casinos available yet</h2>
                <p style="color: #666; margin-top: 10px;">Check back soon for our casino rankings!</p>
            </div>
        <?php else: ?>
            <?php foreach($casinos as $index => $casino): ?>
            <div class="casino-card">
                <div class="casino-logo">
                    <?php if($casino['logo_url']): ?>
                        <img src="<?= htmlspecialchars($casino['logo_url']) ?>" alt="<?= htmlspecialchars($casino['name']) ?>">
                    <?php else: ?>
                        <div style="font-size: 48px;">🎰</div>
                    <?php endif; ?>
                </div>
                
                <div class="casino-info">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: #3b82f6; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 20px;"><?= $index + 1 ?></span>
                        <h2><?= htmlspecialchars($casino['name']) ?></h2>
                    </div>
                    <div class="casino-rating">⭐ <?= $casino['rating'] ?> / 5.0</div>
                    <?php if($casino['bonus']): ?>
                    <div class="casino-bonus">🎁 <?= htmlspecialchars($casino['bonus']) ?></div>
                    <?php endif; ?>
                    <p class="casino-description"><?= htmlspecialchars($casino['description']) ?></p>
                </div>
                
                <div class="casino-action">
                    <?php if($casino['affiliate_link']): ?>
                        <a href="<?= htmlspecialchars($casino['affiliate_link']) ?>" target="_blank" class="btn-play">Play Now →</a>
                    <?php endif; ?>
                    <div style="margin-top: 15px; font-size: 12px; color: #999;">21+ T&C Apply</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="footer">
        <div class="footer-container">
            <p>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. 21+ Only. Gamble Responsibly.</p>
        </div>
    </div>
</body>
</html>