<?php
/**
 * Wishlist Controller
 * Handles wishlist and compare functionality
 */

class WishlistController {
    private $db;
    private $cookieName = 'woodmart_wishlist';
    private $compareCookieName = 'woodmart_compare';
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->initSession();
    }
    
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Add to wishlist
     */
    public function addToWishlist($productId) {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            // Logged in user - store in database
            try {
                $this->db->insert('wishlist', [
                    'user_id' => $userId,
                    'product_id' => $productId
                ]);
                return ['success' => true, 'message' => 'Added to wishlist'];
            } catch (Exception $e) {
                // Might be duplicate
                return ['success' => false, 'message' => 'Already in wishlist'];
            }
        } else {
            // Guest user - store in cookie
            $wishlist = $this->getWishlistFromCookie();
            if (!in_array($productId, $wishlist)) {
                $wishlist[] = $productId;
                $this->saveWishlistToCookie($wishlist);
                return ['success' => true, 'message' => 'Added to wishlist'];
            }
            return ['success' => false, 'message' => 'Already in wishlist'];
        }
    }
    
    /**
     * Remove from wishlist
     */
    public function removeFromWishlist($productId) {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            $this->db->delete('wishlist', 
                'user_id = :user_id AND product_id = :product_id',
                ['user_id' => $userId, 'product_id' => $productId]
            );
        } else {
            $wishlist = $this->getWishlistFromCookie();
            $wishlist = array_diff($wishlist, [$productId]);
            $this->saveWishlistToCookie($wishlist);
        }
        
        return ['success' => true, 'message' => 'Removed from wishlist'];
    }
    
    /**
     * Get wishlist items
     */
    public function getWishlistItems() {
        $userId = $_SESSION['user_id'] ?? null;
        $productIds = [];
        
        if ($userId) {
            $sql = "SELECT product_id FROM wishlist WHERE user_id = :user_id";
            $results = $this->db->fetchAll($sql, ['user_id' => $userId]);
            $productIds = array_column($results, 'product_id');
        } else {
            $productIds = $this->getWishlistFromCookie();
        }
        
        if (empty($productIds)) {
            return [];
        }
        
        // Get product details
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id IN ($placeholders) AND p.status = 'active'";
        
        return $this->db->fetchAll($sql, $productIds);
    }
    
    /**
     * Get wishlist count
     */
    public function getWishlistCount() {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id";
            $result = $this->db->fetch($sql, ['user_id' => $userId]);
            return $result['count'] ?? 0;
        } else {
            return count($this->getWishlistFromCookie());
        }
    }
    
    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($productId) {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            $sql = "SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
            $result = $this->db->fetch($sql, ['user_id' => $userId, 'product_id' => $productId]);
            return !empty($result);
        } else {
            $wishlist = $this->getWishlistFromCookie();
            return in_array($productId, $wishlist);
        }
    }
    
    // ==========================================
    // COMPARE FUNCTIONALITY
    // ==========================================
    
    /**
     * Add to compare
     */
    public function addToCompare($productId) {
        $compare = $this->getCompareFromCookie();
        
        if (count($compare) >= 4) {
            return ['success' => false, 'message' => 'Maximum 4 products can be compared'];
        }
        
        if (!in_array($productId, $compare)) {
            $compare[] = $productId;
            $this->saveCompareToCookie($compare);
            return ['success' => true, 'message' => 'Added to compare'];
        }
        
        return ['success' => false, 'message' => 'Already in compare list'];
    }
    
    /**
     * Remove from compare
     */
    public function removeFromCompare($productId) {
        $compare = $this->getCompareFromCookie();
        $compare = array_diff($compare, [$productId]);
        $this->saveCompareToCookie($compare);
        
        return ['success' => true, 'message' => 'Removed from compare'];
    }
    
    /**
     * Get compare items
     */
    public function getCompareItems() {
        $productIds = $this->getCompareFromCookie();
        
        if (empty($productIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id IN ($placeholders) AND p.status = 'active'";
        
        return $this->db->fetchAll($sql, $productIds);
    }
    
    /**
     * Get compare count
     */
    public function getCompareCount() {
        return count($this->getCompareFromCookie());
    }
    
    /**
     * Clear compare
     */
    public function clearCompare() {
        $this->saveCompareToCookie([]);
        return ['success' => true, 'message' => 'Compare list cleared'];
    }
    
    // ==========================================
    // COOKIE HELPERS
    // ==========================================
    
    private function getWishlistFromCookie() {
        if (isset($_COOKIE[$this->cookieName])) {
            return json_decode($_COOKIE[$this->cookieName], true) ?: [];
        }
        return [];
    }
    
    private function saveWishlistToCookie($wishlist) {
        setcookie(
            $this->cookieName,
            json_encode(array_values($wishlist)),
            time() + (86400 * 30), // 30 days
            '/'
        );
    }
    
    private function getCompareFromCookie() {
        if (isset($_COOKIE[$this->compareCookieName])) {
            return json_decode($_COOKIE[$this->compareCookieName], true) ?: [];
        }
        return [];
    }
    
    private function saveCompareToCookie($compare) {
        setcookie(
            $this->compareCookieName,
            json_encode(array_values($compare)),
            time() + (86400 * 7), // 7 days
            '/'
        );
    }
    
    /**
     * Migrate guest wishlist to user account on login
     */
    public function migrateGuestWishlist($userId) {
        $guestWishlist = $this->getWishlistFromCookie();
        
        if (empty($guestWishlist)) {
            return;
        }
        
        foreach ($guestWishlist as $productId) {
            try {
                $this->db->insert('wishlist', [
                    'user_id' => $userId,
                    'product_id' => $productId
                ]);
            } catch (Exception $e) {
                // Skip duplicates
                continue;
            }
        }
        
        // Clear cookie
        $this->saveWishlistToCookie([]);
    }
}
