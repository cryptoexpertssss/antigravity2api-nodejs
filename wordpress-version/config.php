<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'gaming_today');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// Security
define('SECURE_KEY', 'change-this-to-random-string-' . bin2hex(random_bytes(32)));

// Site Configuration
define('SITE_NAME', 'GamingToday');
define('SITE_URL', 'http://yourdomain.com');

// Admin Configuration
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Start session
session_start();

// Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

?>