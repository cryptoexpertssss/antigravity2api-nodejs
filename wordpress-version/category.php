<?php
require_once 'config.php';
require_once 'includes/functions.php';

$category_id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

// Get articles in this category
$stmt = $pdo->prepare("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.category_id = ? AND a.status='published' ORDER BY a.created_at DESC");
$stmt->execute([$category_id]);
$articles = $stmt->fetchAll();

// Get categories for nav
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($category['name']) ?> - <?= SITE_NAME ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; background: #f9fafb; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; }
        .logo { font-size: 28px; font-weight: 800; color: #1a1a1a; text-decoration: none; }
        .logo span { color: #3b82f6; }
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .nav-links a { color: #4b5563; text-decoration: none; font-weight: 600; font-size: 14px; }
        .auth-buttons { display: flex; gap: 15px; align-items: center; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-header { margin-bottom: 40px; }
        .page-header h1 { font-size: 42px; margin-bottom: 10px; }
        .page-header p { color: #666; font-size: 18px; }
        .articles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .article-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .article-card img { width: 100%; height: 200px; object-fit: cover; }
        .article-card-content { padding: 20px; }
        .article-card h3 { font-size: 20px; margin-bottom: 10px; }
        .article-card h3 a { color: #333; text-decoration: none; }
        .article-card h3 a:hover { color: #3b82f6; }
        .article-meta { font-size: 12px; color: #6b7280; margin-bottom: 10px; }
        .footer { background: #1f2937; color: white; padding: 40px 20px; margin-top: 60px; text-align: center; }
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
                    <a href="category.php?id=<?= $cat['id'] ?>" style="<?= $cat['id'] == $category_id ? 'color: #3b82f6;' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
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
        <div class="page-header">
            <h1><?= htmlspecialchars($category['name']) ?></h1>
            <p><?= htmlspecialchars($category['description']) ?></p>
        </div>

        <?php if(empty($articles)): ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 12px;">
                <h2>No articles in this category yet</h2>
                <p style="color: #666; margin-top: 10px;">Check back soon for new content!</p>
            </div>
        <?php else: ?>
            <div class="articles-grid">
                <?php foreach($articles as $article): ?>
                <div class="article-card">
                    <?php if($article['featured_image']): ?>
                    <img src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                    <?php endif; ?>
                    <div class="article-card-content">
                        <div class="article-meta"><?= date('F j, Y', strtotime($article['created_at'])) ?> • <?= htmlspecialchars($article['author']) ?></div>
                        <h3><a href="article.php?slug=<?= $article['slug'] ?>"><?= htmlspecialchars($article['title']) ?></a></h3>
                        <p style="color: #666; margin-top: 10px;"><?= htmlspecialchars(substr($article['excerpt'], 0, 150)) ?>...</p>
                        <a href="article.php?slug=<?= $article['slug'] ?>" style="color: #3b82f6; font-weight: 600; text-decoration: none; margin-top: 15px; display: inline-block;">Read More →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</body>
</html>