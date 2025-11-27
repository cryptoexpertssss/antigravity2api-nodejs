<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Get article by slug
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = ? AND a.status='published'");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: index.php');
    exit;
}

// Get active ads
$ads_top = $pdo->query("SELECT code FROM ads WHERE position='article-top' AND status='active' LIMIT 1")->fetch();
$ads_middle = $pdo->query("SELECT code FROM ads WHERE position='article-middle' AND status='active' LIMIT 1")->fetch();
$ads_bottom = $pdo->query("SELECT code FROM ads WHERE position='article-bottom' AND status='active' LIMIT 1")->fetch();
$ads_sidebar = $pdo->query("SELECT code FROM ads WHERE position='sidebar' AND status='active' LIMIT 1")->fetch();

// Get affiliate links
$affiliate_links = $pdo->query("SELECT * FROM affiliate_links WHERE status='active' LIMIT 3")->fetchAll();

// Get categories for nav
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($article['title']) ?> - <?= SITE_NAME ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; background: #f9fafb; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; }
        .logo { font-size: 28px; font-weight: 800; color: #1a1a1a; text-decoration: none; }
        .logo span { color: #3b82f6; }
        .nav-links { display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .nav-links a { color: #4b5563; text-decoration: none; font-weight: 600; font-size: 14px; }
        .auth-buttons { display: flex; gap: 15px; align-items: center; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 300px; gap: 40px; }
        .article-header { margin-bottom: 30px; }
        .article-header h1 { font-size: 42px; line-height: 1.2; margin-bottom: 20px; }
        .article-meta { color: #6b7280; font-size: 14px; margin-bottom: 30px; }
        .featured-image { width: 100%; max-height: 500px; object-fit: cover; border-radius: 12px; margin-bottom: 30px; }
        .article-content { background: white; padding: 40px; border-radius: 12px; line-height: 1.8; font-size: 18px; }
        .ad-container { background: #f3f4f6; padding: 20px; text-align: center; margin: 30px 0; border-radius: 8px; }
        .sidebar { position: sticky; top: 100px; }
        .sidebar-section { background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; }
        .sidebar-section h3 { margin-bottom: 20px; font-size: 18px; }
        .affiliate-link { display: block; padding: 15px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 10px; font-weight: 600; text-align: center; }
        .affiliate-link:hover { background: #2563eb; }
        .footer { background: #1f2937; color: white; padding: 40px 20px; margin-top: 60px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Gaming<span>Today</span></a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="casinos.php">Casinos</a>
                <?php foreach(array_slice($categories, 0, 2) as $cat): ?>
                    <a href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="auth-buttons">
                <?php 
                $user_logged_in = isset($_SESSION['user_id']);
                if ($user_logged_in): 
                ?>
                    <a href="user-dashboard.php">Dashboard</a>
                    <a href="user-logout.php" style="color: #dc2626;">Logout</a>
                <?php else: ?>
                    <a href="user-login.php" style="color: #4b5563; font-weight: 600;">Login</a>
                    <a href="register.php" style="background: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <main>
            <!-- Top Ad -->
            <?php if($ads_top): ?>
                <div class="ad-container">
                    <?= $ads_top['code'] ?>
                </div>
            <?php endif; ?>

            <div class="article-header">
                <div style="color: #3b82f6; font-weight: 600; margin-bottom: 10px;"><?= htmlspecialchars($article['category_name']) ?></div>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
                <div class="article-meta">
                    By <?= htmlspecialchars($article['author']) ?> • <?= date('F j, Y', strtotime($article['created_at'])) ?>
                </div>
            </div>

            <?php if($article['featured_image']): ?>
                <img src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="featured-image">
            <?php endif; ?>

            <div class="article-content">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>

            <!-- Middle Ad -->
            <?php if($ads_middle): ?>
                <div class="ad-container">
                    <?= $ads_middle['code'] ?>
                </div>
            <?php endif; ?>

            <!-- Bottom Ad -->
            <?php if($ads_bottom): ?>
                <div class="ad-container">
                    <?= $ads_bottom['code'] ?>
                </div>
            <?php endif; ?>
        </main>

        <aside class="sidebar">
            <!-- Sidebar Ad -->
            <?php if($ads_sidebar): ?>
                <div class="sidebar-section">
                    <h3>Sponsored</h3>
                    <?= $ads_sidebar['code'] ?>
                </div>
            <?php endif; ?>

            <!-- Affiliate Links -->
            <?php if(!empty($affiliate_links)): ?>
                <div class="sidebar-section">
                    <h3>🎖 Recommended Offers</h3>
                    <?php foreach($affiliate_links as $link): ?>
                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" class="affiliate-link">
                            <?= htmlspecialchars($link['name']) ?> →
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <div class="footer">
        <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
            <p>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>