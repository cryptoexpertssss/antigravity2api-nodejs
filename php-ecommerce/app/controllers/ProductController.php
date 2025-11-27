<?php
/**
 * Product Controller
 * Handles single product operations
 */

class ProductController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get product by slug
     */
    public function getProductBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = :slug AND p.status = 'active'
                LIMIT 1";
        
        $product = $this->db->fetch($sql, ['slug' => $slug]);
        
        if ($product) {
            // Parse JSON attributes
            if (!empty($product['json_attributes'])) {
                $product['json_attributes'] = json_decode($product['json_attributes'], true);
            }
            
            // Get images
            $product['images'] = $this->getProductImages($product['id']);
            
            // Get variations
            $product['variations'] = $this->getProductVariations($product['id']);
            
            // Get reviews
            $product['reviews'] = $this->getProductReviews($product['id']);
            
            // Increment view count
            $this->incrementViewCount($product['id']);
        }
        
        return $product;
    }
    
    /**
     * Get product images
     */
    public function getProductImages($productId) {
        $sql = "SELECT * FROM product_images 
                WHERE product_id = :product_id 
                ORDER BY is_primary DESC, sort_order ASC";
        
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }
    
    /**
     * Get product variations
     */
    public function getProductVariations($productId) {
        $sql = "SELECT * FROM variations 
                WHERE product_id = :product_id AND is_active = 1
                ORDER BY is_default DESC, sort_order ASC";
        
        $variations = $this->db->fetchAll($sql, ['product_id' => $productId]);
        
        // Parse JSON attributes
        foreach ($variations as &$variation) {
            if (!empty($variation['attributes'])) {
                $variation['attributes'] = json_decode($variation['attributes'], true);
            }
        }
        
        return $variations;
    }
    
    /**
     * Get product reviews
     */
    public function getProductReviews($productId, $limit = 10) {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.email
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.product_id = :product_id AND r.is_approved = 1
                ORDER BY r.created_at DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'product_id' => $productId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Increment product view count
     */
    private function incrementViewCount($productId) {
        $sql = "UPDATE products SET views_count = views_count + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $productId]);
    }
    
    /**
     * Get variation by ID
     */
    public function getVariation($variationId) {
        $sql = "SELECT * FROM variations WHERE id = :id AND is_active = 1";
        $variation = $this->db->fetch($sql, ['id' => $variationId]);
        
        if ($variation && !empty($variation['attributes'])) {
            $variation['attributes'] = json_decode($variation['attributes'], true);
        }
        
        return $variation;
    }
    
    /**
     * Get related products
     */
    public function getRelatedProducts($productId, $categoryId, $limit = 4) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = :category_id 
                AND p.id != :product_id 
                AND p.status = 'active'
                ORDER BY p.sales_count DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'category_id' => $categoryId,
            'product_id' => $productId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Submit product review
     */
    public function submitReview($productId, $userId, $rating, $title, $comment) {
        $data = [
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'is_approved' => false // Require admin approval
        ];
        
        return $this->db->insert('reviews', $data);
    }
}
