<div class="sidebar">
    <h2>Gaming<span>Today</span></h2>
    
    <ul>
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">📊 Dashboard</a></li>
        <li><a href="articles.php" class="<?= basename($_SERVER['PHP_SELF']) == 'articles.php' ? 'active' : '' ?>">📝 Articles</a></li>
        <li><a href="categories.php" class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">📂 Categories</a></li>
        <li><a href="casinos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'casinos.php' ? 'active' : '' ?>">🎰 Casinos</a></li>
        <li><a href="reviews.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : '' ?>">⭐ Reviews</a></li>
        <li><a href="affiliate.php" class="<?= basename($_SERVER['PHP_SELF']) == 'affiliate.php' ? 'active' : '' ?>">🔗 Affiliate Links</a></li>
        <li><a href="ads.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ads.php' ? 'active' : '' ?>">📢 Advertisements</a></li>
    </ul>
    
    <form method="POST" action="logout.php" style="margin-top: 30px;">
        <button type="submit" class="logout-btn">🚪 Logout</button>
    </form>
</div>