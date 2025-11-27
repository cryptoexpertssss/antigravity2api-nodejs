<?php
/**
 * Cart Controller
 * Handles shopping cart operations
 */

class CartController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->initSession();
    }
    
    /**
     * Initialize session
     */
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = uniqid('cart_', true);
        }
    }
    
    /**
     * Add item to cart
     */
    public function addToCart($productId, $quantity = 1, $variationId = null) {
        // Get product details
        $sql = "SELECT id, name, price, stock_quantity FROM products WHERE id = :id AND status = 'active'";
        $product = $this->db->fetch($sql, ['id' => $productId]);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        // Check if variation exists and get its price/stock
        $price = $product['price'];
        $stockAvailable = $product['stock_quantity'];
        
        if ($variationId) {
            $sql = "SELECT price, stock_quantity FROM variations WHERE id = :id AND is_active = 1";
            $variation = $this->db->fetch($sql, ['id' => $variationId]);
            
            if ($variation) {
                if ($variation['price']) {
                    $price = $variation['price'];
                }
                $stockAvailable = $variation['stock_quantity'];
            }
        }
        
        // Check stock availability
        if ($stockAvailable < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        // Check if item already in cart
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $_SESSION['cart_session_id'];
        
        $checkSql = "SELECT id, quantity FROM cart 
                     WHERE product_id = :product_id 
                     AND " . ($userId ? "user_id = :user_id" : "session_id = :session_id");
        
        $params = ['product_id' => $productId];
        if ($userId) {
            $params['user_id'] = $userId;
        } else {
            $params['session_id'] = $sessionId;
        }
        
        if ($variationId) {
            $checkSql .= " AND variation_id = :variation_id";
            $params['variation_id'] = $variationId;
        }
        
        $existing = $this->db->fetch($checkSql, $params);
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            
            if ($newQuantity > $stockAvailable) {
                return ['success' => false, 'message' => 'Insufficient stock for requested quantity'];
            }
            
            $this->db->update('cart', 
                ['quantity' => $newQuantity],
                'id = :id',
                ['id' => $existing['id']]
            );
        } else {
            // Insert new cart item
            $data = [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $productId,
                'variation_id' => $variationId,
                'quantity' => $quantity,
                'price' => $price
            ];
            
            $this->db->insert('cart', $data);
        }
        
        $cartCount = $this->getCartCount();
        $cartTotal = $this->getCartTotal();
        
        return [
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ];
    }
    
    /**
     * Update cart item quantity
     */
    public function updateCartItem($cartItemId, $quantity) {
        // Validate quantity
        if ($quantity < 1) {
            return $this->removeFromCart($cartItemId);
        }
        
        // Get cart item
        $sql = "SELECT c.*, p.stock_quantity as product_stock, v.stock_quantity as variation_stock
                FROM cart c
                LEFT JOIN products p ON c.product_id = p.id
                LEFT JOIN variations v ON c.variation_id = v.id
                WHERE c.id = :id";
        
        $item = $this->db->fetch($sql, ['id' => $cartItemId]);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Cart item not found'];
        }
        
        // Check stock
        $stockAvailable = $item['variation_id'] ? $item['variation_stock'] : $item['product_stock'];
        
        if ($quantity > $stockAvailable) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        // Update quantity
        $this->db->update('cart',
            ['quantity' => $quantity],
            'id = :id',
            ['id' => $cartItemId]
        );
        
        return [
            'success' => true,
            'message' => 'Cart updated',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal()
        ];
    }
    
    /**
     * Remove item from cart
     */
    public function removeFromCart($cartItemId) {
        $this->db->delete('cart', 'id = :id', ['id' => $cartItemId]);
        
        return [
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal()
        ];
    }
    
    /**
     * Get cart items
     */
    public function getCartItems() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $_SESSION['cart_session_id'];
        
        $sql = "SELECT c.*, p.name, p.slug, p.image, 
                v.attributes, v.sku as variation_sku,
                (c.quantity * c.price) as subtotal
                FROM cart c
                LEFT JOIN products p ON c.product_id = p.id
                LEFT JOIN variations v ON c.variation_id = v.id
                WHERE " . ($userId ? "c.user_id = :user_id" : "c.session_id = :session_id");
        
        $params = $userId ? ['user_id' => $userId] : ['session_id' => $sessionId];
        
        $items = $this->db->fetchAll($sql, $params);
        
        // Parse variation attributes
        foreach ($items as &$item) {
            if (!empty($item['attributes'])) {
                $item['attributes'] = json_decode($item['attributes'], true);
            }
        }
        
        return $items;
    }
    
    /**
     * Get cart count
     */
    public function getCartCount() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $_SESSION['cart_session_id'];
        
        $sql = "SELECT SUM(quantity) as total FROM cart 
                WHERE " . ($userId ? "user_id = :user_id" : "session_id = :session_id");
        
        $params = $userId ? ['user_id' => $userId] : ['session_id' => $sessionId];
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Get cart total
     */
    public function getCartTotal() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $_SESSION['cart_session_id'];
        
        $sql = "SELECT SUM(quantity * price) as total FROM cart 
                WHERE " . ($userId ? "user_id = :user_id" : "session_id = :session_id");
        
        $params = $userId ? ['user_id' => $userId] : ['session_id' => $sessionId];
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Clear cart
     */
    public function clearCart() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $_SESSION['cart_session_id'];
        
        $where = $userId ? "user_id = :user_id" : "session_id = :session_id";
        $params = $userId ? ['user_id' => $userId] : ['session_id' => $sessionId];
        
        $this->db->delete('cart', $where, $params);
        
        return ['success' => true, 'message' => 'Cart cleared'];
    }
    
    /**
     * Apply coupon
     */
    public function applyCoupon($code) {
        $sql = "SELECT * FROM coupons 
                WHERE code = :code 
                AND is_active = 1
                AND (start_date IS NULL OR start_date <= NOW())
                AND (end_date IS NULL OR end_date >= NOW())
                LIMIT 1";
        
        $coupon = $this->db->fetch($sql, ['code' => strtoupper($code)]);
        
        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon'];
        }
        
        // Check usage limit
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['success' => false, 'message' => 'Coupon usage limit reached'];
        }
        
        // Check minimum purchase
        $cartTotal = $this->getCartTotal();
        if ($cartTotal < $coupon['min_purchase']) {
            return ['success' => false, 'message' => 'Minimum purchase amount not met'];
        }
        
        // Calculate discount
        $discount = 0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($cartTotal * $coupon['discount_value']) / 100;
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        } else {
            $discount = $coupon['discount_value'];
        }
        
        $_SESSION['coupon'] = [
            'code' => $coupon['code'],
            'discount' => $discount,
            'type' => $coupon['discount_type']
        ];
        
        return [
            'success' => true,
            'message' => 'Coupon applied successfully',
            'discount' => $discount,
            'new_total' => $cartTotal - $discount
        ];
    }
}
