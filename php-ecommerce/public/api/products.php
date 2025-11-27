<?php
/**
 * Products API Endpoint
 * AJAX product filtering and search
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/app/helpers/Database.php';
require_once dirname(__DIR__, 2) . '/app/controllers/ShopController.php';

$shopController = new ShopController();

try {
    // Get filters from request
    $filters = [];
    
    if (isset($_GET['category_id'])) {
        $filters['category_id'] = (int)$_GET['category_id'];
    }
    
    if (isset($_GET['product_type'])) {
        $filters['product_type'] = $_GET['product_type'];
    }
    
    if (isset($_GET['min_price'])) {
        $filters['min_price'] = (float)$_GET['min_price'];
    }
    
    if (isset($_GET['max_price'])) {
        $filters['max_price'] = (float)$_GET['max_price'];
    }
    
    if (isset($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    
    if (isset($_GET['is_featured'])) {
        $filters['is_featured'] = true;
    }
    
    if (isset($_GET['is_new_arrival'])) {
        $filters['is_new_arrival'] = true;
    }
    
    if (isset($_GET['is_on_sale'])) {
        $filters['is_on_sale'] = true;
    }
    
    if (isset($_GET['order_by'])) {
        $filters['order_by'] = $_GET['order_by'];
    }
    
    if (isset($_GET['page'])) {
        $filters['page'] = (int)$_GET['page'];
    }
    
    if (isset($_GET['limit'])) {
        $filters['limit'] = (int)$_GET['limit'];
    }
    
    // Get products
    $products = $shopController->getProducts($filters);
    $total = $shopController->getProductCount($filters);
    
    echo json_encode([
        'success' => true,
        'data' => $products,
        'total' => $total,
        'page' => $filters['page'] ?? 1,
        'limit' => $filters['limit'] ?? 12
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch products',
        'error' => $e->getMessage()
    ]);
}
