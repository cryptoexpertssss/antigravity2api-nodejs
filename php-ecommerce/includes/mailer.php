<?php
/**
 * Email Helper
 * Send order confirmations and notifications
 */

class Mailer {
    private $from = 'noreply@woodmart.com';
    private $fromName = 'WoodMart Store';
    
    /**
     * Send order confirmation email
     */
    public function sendOrderEmail($orderId) {
        try {
            $db = Database::getInstance();
            
            // Get order details
            $sql = "SELECT o.*, u.email, u.first_name, u.last_name
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = :id";
            
            $order = $db->fetch($sql, ['id' => $orderId]);
            
            if (!$order) {
                return false;
            }
            
            // Get order items
            $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
            $items = $db->fetchAll($sql, ['order_id' => $orderId]);
            
            $order['items'] = $items;
            $order['shipping_address'] = json_decode($order['shipping_address'], true);
            
            // Generate email HTML
            $html = $this->generateOrderEmailHTML($order);
            
            // Send to customer
            $this->sendEmail(
                $order['email'],
                "Order Confirmation - #{$order['order_number']}",
                $html
            );
            
            // Send to admin
            $this->sendEmail(
                'admin@woodmart.com',
                "New Order - #{$order['order_number']}",
                $html
            );
            
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate order email HTML
     */
    private function generateOrderEmailHTML($order) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background: #ff6b6b;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    border-radius: 8px 8px 0 0;
                }
                .content {
                    background: #f9f9f9;
                    padding: 30px;
                    border: 1px solid #ddd;
                }
                .order-details {
                    background: white;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 8px;
                }
                .order-items {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                .order-items th {
                    background: #f0f0f0;
                    padding: 10px;
                    text-align: left;
                    border-bottom: 2px solid #ddd;
                }
                .order-items td {
                    padding: 10px;
                    border-bottom: 1px solid #ddd;
                }
                .total-row {
                    font-weight: bold;
                    font-size: 1.2em;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    color: #666;
                    font-size: 0.9em;
                }
                .button {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #ff6b6b;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Order Confirmation</h1>
            </div>
            
            <div class="content">
                <h2>Thank you for your order!</h2>
                <p>Hi <?php echo htmlspecialchars($order['first_name']); ?>,</p>
                <p>Your order has been received and is being processed. Here are your order details:</p>
                
                <div class="order-details">
                    <h3>Order Information</h3>
                    <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                    <p><strong>Status:</strong> <?php echo strtoupper($order['status']); ?></p>
                </div>
                
                <h3>Order Items</h3>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;">Subtotal:</td>
                            <td>$<?php echo number_format($order['subtotal'], 2); ?></td>
                        </tr>
                        <?php if ($order['tax'] > 0): ?>
                            <tr>
                                <td colspan="3" style="text-align: right;">Tax:</td>
                                <td>$<?php echo number_format($order['tax'], 2); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($order['shipping_cost'] > 0): ?>
                            <tr>
                                <td colspan="3" style="text-align: right;">Shipping:</td>
                                <td>$<?php echo number_format($order['shipping_cost'], 2); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($order['discount'] > 0): ?>
                            <tr>
                                <td colspan="3" style="text-align: right;">Discount:</td>
                                <td>-$<?php echo number_format($order['discount'], 2); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;">Total:</td>
                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="order-details">
                    <h3>Shipping Address</h3>
                    <?php if (!empty($order['shipping_address'])): ?>
                        <p>
                            <?php echo htmlspecialchars($order['shipping_address']['name'] ?? $order['first_name'] . ' ' . $order['last_name']); ?><br>
                            <?php echo htmlspecialchars($order['shipping_address']['address'] ?? ''); ?><br>
                            <?php echo htmlspecialchars($order['shipping_address']['city'] ?? ''); ?>, 
                            <?php echo htmlspecialchars($order['shipping_address']['state'] ?? ''); ?> 
                            <?php echo htmlspecialchars($order['shipping_address']['zip'] ?? ''); ?><br>
                            <?php echo htmlspecialchars($order['shipping_address']['phone'] ?? ''); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center;">
                    <a href="<?php echo 'http://' . $_SERVER['HTTP_HOST']; ?>/user/order-details.php?id=<?php echo $order['id']; ?>" class="button">
                        View Order Details
                    </a>
                </div>
            </div>
            
            <div class="footer">
                <p>This is an automated email. Please do not reply to this message.</p>
                <p>&copy; <?php echo date('Y'); ?> WoodMart. All rights reserved.</p>
                <p>If you have any questions, please contact us at support@woodmart.com</p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Send email using PHP mail()
     */
    private function sendEmail($to, $subject, $html) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$this->fromName} <{$this->from}>",
            'Reply-To: ' . $this->from,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($to, $subject, $html, implode("\r\n", $headers));
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($email, $resetToken) {
        $resetUrl = "http://{$_SERVER['HTTP_HOST']}/reset-password.php?token={$resetToken}";
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { display: inline-block; padding: 12px 30px; background: #ff6b6b; 
                          color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Password Reset Request</h2>
                <p>You requested to reset your password. Click the button below to reset it:</p>
                <p><a href='{$resetUrl}' class='button'>Reset Password</a></p>
                <p>If you didn't request this, please ignore this email.</p>
                <p>This link will expire in 1 hour.</p>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($email, 'Password Reset Request', $html);
    }
    
    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($email, $firstName) {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                .header { background: #ff6b6b; color: white; padding: 20px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to WoodMart!</h1>
                </div>
                <div style='padding: 20px;'>
                    <h2>Hi {$firstName},</h2>
                    <p>Thank you for creating an account with WoodMart!</p>
                    <p>You can now:</p>
                    <ul>
                        <li>Track your orders</li>
                        <li>Save items to your wishlist</li>
                        <li>Get faster checkout</li>
                        <li>Receive exclusive offers</li>
                    </ul>
                    <p>Happy shopping!</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($email, 'Welcome to WoodMart!', $html);
    }
}

/**
 * Quick helper function
 */
function sendOrderEmail($orderId) {
    $mailer = new Mailer();
    return $mailer->sendOrderEmail($orderId);
}
