<?php
/**
 * Wishlist API Endpoint
 * AJAX wishlist operations
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/app/helpers/Database.php';
require_once dirname(__DIR__, 2) . '/app/controllers/WishlistController.php';

$wishlistController = new WishlistController();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // GET - Get wishlist items
    if ($method === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'count') {
            $count = $wishlistController->getWishlistCount();
            echo json_encode(['success' => true, 'count' => $count]);
        } elseif (isset($_GET['action']) && $_GET['action'] === 'check') {
            $productId = $_GET['product_id'] ?? null;
            if (!$productId) {
                throw new Exception('Product ID required');
            }
            $isInWishlist = $wishlistController->isInWishlist($productId);
            echo json_encode(['success' => true, 'in_wishlist' => $isInWishlist]);
        } else {
            $items = $wishlistController->getWishlistItems();
            echo json_encode(['success' => true, 'items' => $items]);
        }
    }
    
    // POST - Add/Remove from wishlist
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['action'])) {
            throw new Exception('Action parameter required');
        }
        
        switch ($data['action']) {
            case 'add':
                $productId = $data['product_id'] ?? null;
                if (!$productId) {
                    throw new Exception('Product ID required');
                }
                $result = $wishlistController->addToWishlist($productId);
                echo json_encode($result);
                break;
                
            case 'remove':
                $productId = $data['product_id'] ?? null;
                if (!$productId) {
                    throw new Exception('Product ID required');
                }
                $result = $wishlistController->removeFromWishlist($productId);
                echo json_encode($result);
                break;
                
            case 'toggle':
                $productId = $data['product_id'] ?? null;
                if (!$productId) {
                    throw new Exception('Product ID required');
                }
                
                if ($wishlistController->isInWishlist($productId)) {
                    $result = $wishlistController->removeFromWishlist($productId);
                } else {
                    $result = $wishlistController->addToWishlist($productId);
                }
                echo json_encode($result);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    }
    
    else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
