<?php
require_once 'config.php';
require_once 'includes/functions.php';

echo "<h1>Testing Navigation Elements</h1>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>isUserLoggedIn():</h2>";
echo isUserLoggedIn() ? 'TRUE (User is logged in)' : 'FALSE (User is NOT logged in)';

echo "<h2>shouldShowAdminLink():</h2>";
echo shouldShowAdminLink() ? 'TRUE (Admin link should show)' : 'FALSE (Admin link should NOT show)';

echo "<h2>Categories:</h2>";
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
echo "<pre>";
print_r($categories);
echo "</pre>";

echo "<h2>Navigation HTML Test:</h2>";
echo '<div style="border: 2px solid red; padding: 20px; background: #f0f0f0;">';
?>
    <nav style="background: white; padding: 20px;">
        <a href="index.php">Home</a> | 
        <a href="casinos.php">Casino Rankings</a> | 
        
        <?php if (isUserLoggedIn()): ?>
            <a href="user-dashboard.php" style="color: green;">My Dashboard</a> |
            <a href="user-logout.php" style="color: red;">Logout</a>
        <?php else: ?>
            <a href="user-login.php" style="color: blue;">Login</a> |
            <a href="register.php" style="color: blue;">Sign Up</a>
        <?php endif; ?>
        
        <?php if (shouldShowAdminLink()): ?>
            | <a href="login.php" style="color: gray;">Admin</a>
        <?php endif; ?>
    </nav>
<?php
echo '</div>';
?>
