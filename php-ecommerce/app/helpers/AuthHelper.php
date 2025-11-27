<?php
/**
 * Authentication Helper
 * Simple session-based authentication for admin panel
 */

class AuthHelper {
    
    /**
     * Start session if not already started
     */
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is logged in as admin
     */
    public static function isAdmin() {
        self::startSession();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Login admin user
     */
    public static function login($email, $password) {
        self::startSession();
        
        // For demo purposes, hardcoded credentials
        // In production, verify against database
        if ($email === 'admin@woodmart.com' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_id'] = 1;
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout admin user
     */
    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }
    
    /**
     * Require admin authentication
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: /admin/login.php');
            exit();
        }
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        self::startSession();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        self::startSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
