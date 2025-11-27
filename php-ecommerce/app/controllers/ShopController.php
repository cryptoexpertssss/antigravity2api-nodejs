<?php
/**
 * Shop Controller
 * Handles product listing, filtering, and search
 */

class ShopController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get products with filters
     */
    public function getProducts($filters = []) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                GROUP_CONCAT(DISTINCT pi.image_path) as images
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id
                WHERE p.status = 'active'";
        
        $params = [];
        
        // Category filter
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        // Product type filter
        if (!empty($filters['product_type'])) {
            $sql .= " AND p.product_type = :product_type";
            $params['product_type'] = $filters['product_type'];
        }
        
        // Price range filter
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        // Search query
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        // Featured filter
        if (!empty($filters['is_featured'])) {
            $sql .= " AND p.is_featured = 1";
        }
        
        // New arrivals filter
        if (!empty($filters['is_new_arrival'])) {
            $sql .= " AND p.is_new_arrival = 1";
        }
        
        // On sale filter
        if (!empty($filters['is_on_sale'])) {
            $sql .= " AND p.is_on_sale = 1";
        }
        
        $sql .= " GROUP BY p.id";
        
        // Sorting
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        
        switch ($orderBy) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'name':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'popular':
                $sql .= " ORDER BY p.sales_count DESC";
                break;
            case 'rating':
                $sql .= " ORDER BY p.rating_avg DESC";
                break;
            default:
                $sql .= " ORDER BY p.created_at DESC";
        }
        
        // Pagination
        $page = $filters['page'] ?? 1;
        $limit = $filters['limit'] ?? 12;
        $offset = ($page - 1) * $limit;
        
        $sql .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        $products = $this->db->fetchAll($sql, $params);
        
        // Process products
        foreach ($products as &$product) {
            if (!empty($product['json_attributes'])) {
                $product['json_attributes'] = json_decode($product['json_attributes'], true);
            }
            if (!empty($product['images'])) {
                $product['images'] = explode(',', $product['images']);
            }
        }
        
        return $products;
    }
    
    /**
     * Get total product count with filters
     */
    public function getProductCount($filters = []) {
        $sql = "SELECT COUNT(DISTINCT p.id) as total FROM products p WHERE p.status = 'active'";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['product_type'])) {
            $sql .= " AND p.product_type = :product_type";
            $params['product_type'] = $filters['product_type'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Get filter options
     */
    public function getFilterOptions() {
        return [
            'price_ranges' => [
                ['min' => 0, 'max' => 50, 'label' => 'Under $50'],
                ['min' => 50, 'max' => 100, 'label' => '$50 - $100'],
                ['min' => 100, 'max' => 200, 'label' => '$100 - $200'],
                ['min' => 200, 'max' => 500, 'label' => '$200 - $500'],
                ['min' => 500, 'max' => null, 'label' => 'Over $500']
            ],
            'brands' => $this->getBrands(),
            'colors' => $this->getColors(),
            'categories' => $this->getCategories()
        ];
    }
    
    /**
     * Get unique brands from products
     */
    private function getBrands() {
        $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(json_attributes, '$.brand')) as brand
                FROM products
                WHERE JSON_EXTRACT(json_attributes, '$.brand') IS NOT NULL
                ORDER BY brand";
        
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'brand');
    }
    
    /**
     * Get unique colors from variations
     */
    private function getColors() {
        $sql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.color')) as color
                FROM variations
                WHERE JSON_EXTRACT(attributes, '$.color') IS NOT NULL
                ORDER BY color";
        
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'color');
    }
    
    /**
     * Get all categories
     */
    private function getCategories() {
        $sql = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY name";
        return $this->db->fetchAll($sql);
    }
}
