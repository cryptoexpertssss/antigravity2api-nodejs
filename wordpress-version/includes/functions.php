<?php
// Helper function to check if admin link should be shown
function shouldShowAdminLink() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'hide_admin_link'");
        $stmt->execute();
        $value = $stmt->fetchColumn();
        return $value !== '1'; // Show if not set to hide
    } catch (Exception $e) {
        return true; // Show by default if table doesn't exist
    }
}

// Check if user is logged in (regular user)
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Require user login
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        header('Location: user-login.php');
        exit;
    }
}
?>