<?php
require_once '../config.php';
requireLogin();

// Get statistics
$stats = [
    'articles' => $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'casinos' => $pdo->query("SELECT COUNT(*) FROM casinos")->fetchColumn(),
    'reviews_pending' => $pdo->query("SELECT COUNT(*) FROM reviews WHERE status='pending'")->fetchColumn()
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - GamingToday</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>Dashboard</h1>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-number"><?= $stats['articles'] ?></div>
                    <div class="stat-label">Total Articles</div>
                    <a href="articles.php" class="stat-link">Manage →</a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📂</div>
                    <div class="stat-number"><?= $stats['categories'] ?></div>
                    <div class="stat-label">Categories</div>
                    <a href="categories.php" class="stat-link">Manage →</a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🎰</div>
                    <div class="stat-number"><?= $stats['casinos'] ?></div>
                    <div class="stat-label">Casino Listings</div>
                    <a href="casinos.php" class="stat-link">Manage →</a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-number"><?= $stats['reviews_pending'] ?></div>
                    <div class="stat-label">Pending Reviews</div>
                    <a href="reviews.php" class="stat-link">Manage →</a>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="articles.php?action=create" class="btn btn-primary">+ New Article</a>
                    <a href="categories.php?action=create" class="btn btn-secondary">+ New Category</a>
                    <a href="casinos.php?action=create" class="btn btn-secondary">+ New Casino</a>
                    <a href="../index.php" class="btn btn-secondary" target="_blank">View Website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>