<?php
/**
 * Order Controller
 * Handles order management
 */

class OrderController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create order from cart
     */
    public function createOrder($userId, $cartItems, $shippingAddress, $billingAddress, $paymentMethod = 'cod') {
        try {
            $this->db->beginTransaction();
            
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            $tax = $subtotal * 0.1; // 10% tax
            $shippingCost = $subtotal > 100 ? 0 : 10; // Free shipping over $100
            $discount = $_SESSION['coupon']['discount'] ?? 0;
            $total = $subtotal + $tax + $shippingCost - $discount;
            
            // Generate order number
            $orderNumber = 'WM' . date('Ymd') . strtoupper(substr(uniqid(), -6));
            
            // Create order
            $orderId = $this->db->insert('orders', [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'paid',
                'shipping_address' => json_encode($shippingAddress),
                'billing_address' => json_encode($billingAddress)
            ]);
            
            // Create order items
            foreach ($cartItems as $item) {
                $this->db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'product_name' => $item['name'],
                    'sku' => $item['sku'] ?? '',
                    'variation_details' => json_encode($item['attributes'] ?? []),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);
                
                // Update product stock
                if ($item['variation_id']) {
                    $sql = "UPDATE variations SET stock_quantity = stock_quantity - :qty WHERE id = :id";
                    $this->db->query($sql, ['qty' => $item['quantity'], 'id' => $item['variation_id']]);
                } else {
                    $sql = "UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id";
                    $this->db->query($sql, ['qty' => $item['quantity'], 'id' => $item['product_id']]);
                }
            }
            
            $this->db->commit();
            
            // Clear coupon session
            unset($_SESSION['coupon']);
            
            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total' => $total
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Order creation failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create order'];
        }
    }
    
    /**
     * Get user orders
     */
    public function getUserOrders($userId, $limit = 10) {
        $sql = "SELECT * FROM orders 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $orders = $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit]);
        
        // Parse JSON addresses
        foreach ($orders as &$order) {
            $order['shipping_address'] = json_decode($order['shipping_address'], true);
            $order['billing_address'] = json_decode($order['billing_address'], true);
        }
        
        return $orders;
    }
    
    /**
     * Get order details
     */
    public function getOrderDetails($orderId, $userId = null) {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $params = ['id' => $orderId];
        
        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $order = $this->db->fetch($sql, $params);
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
        $order['items'] = $this->db->fetchAll($sql, ['order_id' => $orderId]);
        
        // Parse JSON
        $order['shipping_address'] = json_decode($order['shipping_address'], true);
        $order['billing_address'] = json_decode($order['billing_address'], true);
        
        foreach ($order['items'] as &$item) {
            $item['variation_details'] = json_decode($item['variation_details'], true);
        }
        
        return $order;
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $this->db->update('orders',
            ['status' => $status],
            'id = :id',
            ['id' => $orderId]
        );
        
        return ['success' => true, 'message' => 'Order status updated'];
    }
    
    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $userId) {
        // Only pending orders can be cancelled
        $order = $this->getOrderDetails($orderId, $userId);
        
        if (!$order || $order['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Order cannot be cancelled'];
        }
        
        return $this->updateOrderStatus($orderId, 'cancelled');
    }
}
