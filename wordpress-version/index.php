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
        .slider-container { position: relative; max-width: 100%; margin: 0 auto; overflow: hidden; background: #000; }
        .slider-wrapper { display: flex; transition: transform 0.5s ease-in-out; }
        .slide { min-width: 100%; height: 500px; position: relative; }
        .slide img { width: 100%; height: 100%; object-fit: cover; opacity: 0.7; }
        .slide-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white; width: 80%; max-width: 800px; }
        .slide-content h2 { font-size: 48px; margin-bottom: 20px; text-shadow: 2px 2px 8px rgba(0,0,0,0.8); }
        .slide-content p { font-size: 20px; margin-bottom: 30px; text-shadow: 1px 1px 4px rgba(0,0,0,0.8); }
        .slide-btn { display: inline-block; padding: 15px 40px; background: #3b82f6; color: white; text-decoration: none; border-radius: 50px; font-weight: 700; transition: background 0.3s; }
        .slide-btn:hover { background: #2563eb; }
        .slider-nav { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px; z-index: 10; }
        .slider-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.5); cursor: pointer; transition: all 0.3s; }
        .slider-dot.active { background: white; width: 30px; border-radius: 6px; }
        .slider-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.3); color: white; border: none; font-size: 30px; padding: 15px 20px; cursor: pointer; z-index: 10; transition: background 0.3s; }
        .slider-arrow:hover { background: rgba(255,255,255,0.5); }
        .slider-arrow.prev { left: 20px; }
        .slider-arrow.next { right: 20px; }
        @media (max-width: 768px) {
            .slide { height: 400px; }
            .slide-content h2 { font-size: 32px; }
            .slide-content p { font-size: 16px; }
        }
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
                
                <?php if (isUserLoggedIn()): ?>
                    <a href="user-dashboard.php">My Dashboard</a>
                    <a href="user-logout.php" style="color: #dc2626;">Logout</a>
                <?php else: ?>
                    <a href="user-login.php">Login</a>
                    <a href="register.php" style="background: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px;">Sign Up</a>
                <?php endif; ?>
                
                <?php if (shouldShowAdminLink()): ?>
                    <a href="login.php" style="color: #666; font-size: 12px;">Admin</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Slider -->
    <div class="slider-container">
        <div class="slider-wrapper">
            <?php
            // Get top 4 featured articles for slider
            $slider_articles = $pdo->query("SELECT * FROM articles WHERE status='published' ORDER BY created_at DESC LIMIT 4")->fetchAll();
            if(empty($slider_articles)) {
                // Default slides if no articles
                $slider_articles = [
                    ['title' => 'Welcome to GamingToday', 'excerpt' => 'Your ultimate destination for gaming news, casino reviews, and exclusive bonuses!', 'featured_image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1200', 'slug' => '#'],
                    ['title' => 'Top Casino Rankings', 'excerpt' => 'Discover the best online casinos with verified reviews and ratings', 'featured_image' => 'https://images.unsplash.com/photo-1596838132731-3301c3fd4317?w=1200', 'slug' => 'casinos.php'],
                    ['title' => 'Latest Gaming News', 'excerpt' => 'Stay updated with breaking news from the gaming industry', 'featured_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200', 'slug' => '#'],
                ];
            }
            foreach($slider_articles as $index => $slide): 
            ?>
            <div class="slide">
                <img src="<?= htmlspecialchars($slide['featured_image'] ?? 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1200') ?>" alt="<?= htmlspecialchars($slide['title']) ?>">
                <div class="slide-content">
                    <h2><?= htmlspecialchars($slide['title']) ?></h2>
                    <p><?= htmlspecialchars($slide['excerpt']) ?></p>
                    <a href="<?= isset($slide['slug']) && $slide['slug'] != '#' ? 'article.php?slug=' . htmlspecialchars($slide['slug']) : '#' ?>" class="slide-btn">Explore Now →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button class="slider-arrow prev" onclick="moveSlide(-1)">‹</button>
        <button class="slider-arrow next" onclick="moveSlide(1)">›</button>
        
        <div class="slider-nav">
            <?php for($i = 0; $i < count($slider_articles); $i++): ?>
                <div class="slider-dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $i ?>)"></div>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = slides.length;

        function showSlide(n) {
            if (n >= totalSlides) currentSlide = 0;
            if (n < 0) currentSlide = totalSlides - 1;
            
            const slider = document.querySelector('.slider-wrapper');
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function moveSlide(direction) {
            currentSlide += direction;
            showSlide(currentSlide);
        }

        function goToSlide(n) {
            currentSlide = n;
            showSlide(n);
        }

        // Auto-play slider every 5 seconds
        setInterval(() => {
            currentSlide++;
            showSlide(currentSlide);
        }, 5000);
    </script>

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