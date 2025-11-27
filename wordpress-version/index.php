<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Get featured article
$featured = $pdo->query("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status='published' ORDER BY a.created_at DESC LIMIT 1")->fetch();

// Get latest articles
$articles = $pdo->query("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status='published' ORDER BY a.created_at DESC LIMIT 6")->fetchAll();

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= SITE_NAME ?> - Gaming News & Casino Reviews</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 28px; font-weight: 800; color: #1a1a1a; text-decoration: none; }
        .logo span { color: #3b82f6; }
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links a { color: #4b5563; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s; }
        .nav-links a:hover { color: #3b82f6; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 20px; color: white; }
        .hero-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
        .hero h1 { font-size: 48px; margin-bottom: 20px; line-height: 1.2; }
        .hero p { font-size: 18px; margin-bottom: 30px; opacity: 0.9; }
        .hero .btn { padding: 15px 30px; background: white; color: #667eea; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block; }
        .hero img { width: 100%; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        .section-title { font-size: 32px; font-weight: 700; margin-bottom: 30px; }
        .articles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .article-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .article-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .article-card img { width: 100%; height: 200px; object-fit: cover; }
        .article-card-content { padding: 20px; }
        .article-meta { font-size: 12px; color: #6b7280; margin-bottom: 10px; }
        .article-card h3 { font-size: 20px; margin-bottom: 10px; }
        .article-card h3 a { color: #333; text-decoration: none; }
        .article-card h3 a:hover { color: #3b82f6; }
        .article-card p { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
        .read-more { color: #3b82f6; font-weight: 600; text-decoration: none; font-size: 14px; }
        .footer { background: #1f2937; color: white; padding: 40px 20px; margin-top: 60px; }
        .footer-container { max-width: 1200px; margin: 0 auto; }
        .footer p { color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Gaming<span>Today</span></a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="casinos.php">Casino Rankings</a>
                <?php foreach(array_slice($categories, 0, 3) as $cat): ?>
                    <a href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                <?php endforeach; ?>
                <a href="login.php">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <?php if($featured): ?>
    <div class="hero">
        <div class="hero-container">
            <div>
                <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600;">Featured Story</span>
                <h1><?= htmlspecialchars($featured['title']) ?></h1>
                <p><?= htmlspecialchars($featured['excerpt']) ?></p>
                <a href="article.php?slug=<?= $featured['slug'] ?>" class="btn">Read Full Story →</a>
            </div>
            <?php if($featured['featured_image']): ?>
            <div>
                <img src="<?= htmlspecialchars($featured['featured_image']) ?>" alt="<?= htmlspecialchars($featured['title']) ?>">
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Latest Articles -->
    <div class="container">
        <h2 class="section-title">Latest News</h2>
        <div class="articles-grid">
            <?php foreach(array_slice($articles, 1) as $article): ?>
            <div class="article-card">
                <?php if($article['featured_image']): ?>
                <img src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                <?php endif; ?>
                <div class="article-card-content">
                    <div class="article-meta">
                        <?= date('F j, Y', strtotime($article['created_at'])) ?> • <?= htmlspecialchars($article['author']) ?>
                    </div>
                    <h3><a href="article.php?slug=<?= $article['slug'] ?>"><?= htmlspecialchars($article['title']) ?></a></h3>
                    <p><?= htmlspecialchars(substr($article['excerpt'], 0, 150)) ?>...</p>
                    <a href="article.php?slug=<?= $article['slug'] ?>" class="read-more">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-container">
            <p>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. 21+ Only.</p>
        </div>
    </div>
</body>
</html>