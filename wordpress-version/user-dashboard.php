<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('user-login.php');
}

// Get user's reviews
$stmt = $pdo->prepare("SELECT r.*, c.name as casino_name FROM reviews r LEFT JOIN casinos c ON r.casino_id = c.id WHERE r.user_name = ? ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user_username']]);
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Dashboard - GamingToday</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 800; color: #1a1a1a; text-decoration: none; }
        .logo span { color: #3b82f6; }
        .nav-right { display: flex; gap: 20px; align-items: center; }
        .nav-right a { color: #4b5563; text-decoration: none; font-weight: 600; font-size: 14px; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .welcome { background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .welcome h1 { margin-bottom: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .stat-number { font-size: 32px; font-weight: 700; color: #3b82f6; }
        .stat-label { color: #666; font-size: 14px; }
        .reviews-section { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .review-card { border-bottom: 1px solid #e5e7eb; padding: 20px 0; }
        .review-card:last-child { border-bottom: none; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .review-status { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .btn { padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #2563eb; }
        .logout-btn { padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Gaming<span>Today</span></a>
            <div class="nav-right">
                <a href="index.php">Home</a>
                <a href="casinos.php">Casinos</a>
                <span style="color: #666;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <form method="POST" action="user-logout.php" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome">
            <h1>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
            <p style="color: #666;">Manage your reviews and profile</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($reviews) ?></div>
                <div class="stat-label">Total Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($reviews, fn($r) => $r['status'] === 'approved')) ?></div>
                <div class="stat-label">Approved Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($reviews, fn($r) => $r['status'] === 'pending')) ?></div>
                <div class="stat-label">Pending Reviews</div>
            </div>
        </div>

        <div class="reviews-section">
            <h2 style="margin-bottom: 20px;">My Reviews</h2>
            
            <?php if (empty($reviews)): ?>
                <p style="text-align: center; color: #666; padding: 40px;">You haven't submitted any reviews yet.</p>
                <div style="text-align: center;">
                    <a href="casinos.php" class="btn">Browse Casinos</a>
                </div>
            <?php else: ?>
                <?php foreach($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <strong><?= htmlspecialchars($review['title']) ?></strong><br>
                            <small style="color: #666;">Casino: <?= htmlspecialchars($review['casino_name']) ?> | Rating: <?= $review['rating'] ?>★</small>
                        </div>
                        <span class="review-status status-<?= $review['status'] ?>"><?= ucfirst($review['status']) ?></span>
                    </div>
                    <p style="color: #666; font-size: 14px;"><?= htmlspecialchars($review['comment']) ?></p>
                    <small style="color: #999;">Submitted on <?= date('M j, Y', strtotime($review['created_at'])) ?></small>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>