<?php
/**
 * User Dashboard
 * Protected area for logged-in customers
 */

require_once dirname(__DIR__, 2) . '/app/helpers/Database.php';
require_once dirname(__DIR__, 2) . '/app/helpers/AuthHelper.php';
require_once dirname(__DIR__, 2) . '/app/helpers/ThemeHelper.php';
require_once dirname(__DIR__, 2) . '/app/controllers/OrderController.php';

// Require user authentication
AuthHelper::startSession();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userId = $_SESSION['user_id'];
$orderController = new OrderController();
$db = Database::getInstance();

// Get user info
$user = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $userId]);

// Handle form submissions
$message = null;
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $db->update('users',
                    [
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'],
                        'phone' => $_POST['phone']
                    ],
                    'id = :id',
                    ['id' => $userId]
                );
                $message = 'Profile updated successfully';
                break;
                
            case 'change_password':
                // Verify current password
                if (password_verify($_POST['current_password'], $user['password'])) {
                    if ($_POST['new_password'] === $_POST['confirm_password']) {
                        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                        $db->update('users',
                            ['password' => $hashedPassword],
                            'id = :id',
                            ['id' => $userId]
                        );
                        $message = 'Password changed successfully';
                    } else {
                        $message = 'New passwords do not match';
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Current password is incorrect';
                    $messageType = 'danger';
                }
                break;
        }
        
        // Refresh user data
        $user = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
    }
}

// Get user orders
$orders = $orderController->getUserOrders($userId, 10);

// Active tab
$activeTab = $_GET['tab'] ?? 'orders';

$headerLayout = ThemeHelper::getHeaderLayout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - WoodMart</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/dynamic-style.php">
    
    <style>
        .dashboard-sidebar {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .dashboard-sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .dashboard-sidebar .nav-link:hover,
        .dashboard-sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
        }
        
        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s;
        }
        
        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cfe2ff; color: #084298; }
        .status-shipped { background: #d1e7dd; color: #0f5132; }
        .status-delivered { background: #d1e7dd; color: #0a3622; }
        .status-cancelled { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>

<?php include dirname(__DIR__, 2) . "/includes/headers/header-v{$headerLayout}.php"; ?>

<div class="container-custom my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="dashboard-sidebar">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                    <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link <?php echo $activeTab === 'orders' ? 'active' : ''; ?>" href="?tab=orders">
                        <i class="bi bi-box-seam"></i> My Orders
                    </a>
                    <a class="nav-link <?php echo $activeTab === 'addresses' ? 'active' : ''; ?>" href="?tab=addresses">
                        <i class="bi bi-geo-alt"></i> Address Book
                    </a>
                    <a class="nav-link <?php echo $activeTab === 'account' ? 'active' : ''; ?>" href="?tab=account">
                        <i class="bi bi-person"></i> Account Details
                    </a>
                    <a class="nav-link" href="/wishlist.php">
                        <i class="bi bi-heart"></i> My Wishlist
                    </a>
                    <a class="nav-link text-danger" href="/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($activeTab === 'orders'): ?>
                <!-- My Orders -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-box-seam"></i> My Orders</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3 text-muted">No orders yet</p>
                                <a href="/shop.php" class="btn btn-primary">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>Order #<?php echo $order['order_number']; ?></h5>
                                            <p class="text-muted mb-2">
                                                <i class="bi bi-calendar"></i> 
                                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                            </p>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo strtoupper($order['status']); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h4 class="text-primary">$<?php echo number_format($order['total'], 2); ?></h4>
                                            <a href="/user/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php elseif ($activeTab === 'addresses'): ?>
                <!-- Address Book -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-geo-alt"></i> Address Book</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-4">
                                    <h5 class="mb-3">Shipping Address</h5>
                                    <p class="mb-2"><strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong></p>
                                    <p class="mb-2">123 Main Street</p>
                                    <p class="mb-2">City, State 12345</p>
                                    <p class="mb-2">Phone: <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                                    <button class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-4">
                                    <h5 class="mb-3">Billing Address</h5>
                                    <p class="mb-2"><strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong></p>
                                    <p class="mb-2">123 Main Street</p>
                                    <p class="mb-2">City, State 12345</p>
                                    <p class="mb-2">Phone: <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                                    <button class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Note:</strong> Address management will be implemented in checkout flow.
                        </div>
                    </div>
                </div>
                
            <?php elseif ($activeTab === 'account'): ?>
                <!-- Account Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-person"></i> Account Details</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <small class="form-text text-muted">Email cannot be changed</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Change Password -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <small class="form-text text-muted">At least 8 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
