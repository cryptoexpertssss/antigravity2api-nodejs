<?php
/**
 * Cart API Endpoint
 * AJAX cart operations
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/app/helpers/Database.php';
require_once dirname(__DIR__, 2) . '/app/controllers/CartController.php';

$cartController = new CartController();

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // GET - Get cart items
    if ($method === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'count') {
            $count = $cartController->getCartCount();
            echo json_encode(['success' => true, 'count' => $count]);
        } elseif (isset($_GET['action']) && $_GET['action'] === 'total') {
            $total = $cartController->getCartTotal();
            echo json_encode(['success' => true, 'total' => $total]);
        } else {
            $items = $cartController->getCartItems();
            $count = $cartController->getCartCount();
            $total = $cartController->getCartTotal();
            
            echo json_encode([
                'success' => true,
                'items' => $items,
                'count' => $count,
                'total' => $total
            ]);
        }
    }
    
    // POST - Add to cart
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'add':
                    $productId = $data['product_id'] ?? null;
                    $quantity = $data['quantity'] ?? 1;
                    $variationId = $data['variation_id'] ?? null;
                    
                    if (!$productId) {
                        throw new Exception('Product ID required');
                    }
                    
                    $result = $cartController->addToCart($productId, $quantity, $variationId);
                    echo json_encode($result);
                    break;
                    
                case 'update':
                    $cartItemId = $data['cart_item_id'] ?? null;
                    $quantity = $data['quantity'] ?? null;
                    
                    if (!$cartItemId || $quantity === null) {
                        throw new Exception('Cart item ID and quantity required');
                    }
                    
                    $result = $cartController->updateCartItem($cartItemId, $quantity);
                    echo json_encode($result);
                    break;
                    
                case 'remove':
                    $cartItemId = $data['cart_item_id'] ?? null;
                    
                    if (!$cartItemId) {
                        throw new Exception('Cart item ID required');
                    }
                    
                    $result = $cartController->removeFromCart($cartItemId);
                    echo json_encode($result);
                    break;
                    
                case 'clear':
                    $result = $cartController->clearCart();
                    echo json_encode($result);
                    break;
                    
                case 'apply_coupon':
                    $code = $data['coupon_code'] ?? null;
                    
                    if (!$code) {
                        throw new Exception('Coupon code required');
                    }
                    
                    $result = $cartController->applyCoupon($code);
                    echo json_encode($result);
                    break;
                    
                default:
                    throw new Exception('Invalid action');
            }
        } else {
            throw new Exception('Action parameter required');
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
