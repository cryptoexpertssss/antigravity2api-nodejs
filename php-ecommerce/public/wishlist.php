<?php
/**
 * Wishlist Page
 * Display saved products
 */

require_once dirname(__DIR__) . '/app/helpers/Database.php';
require_once dirname(__DIR__) . '/app/helpers/ThemeHelper.php';
require_once dirname(__DIR__) . '/app/controllers/WishlistController.php';

$wishlistController = new WishlistController();
$wishlistItems = $wishlistController->getWishlistItems();
$headerLayout = ThemeHelper::getHeaderLayout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - WoodMart</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/dynamic-style.php">
</head>
<body>

<?php include dirname(__DIR__) . "/includes/headers/header-v{$headerLayout}.php"; ?>

<div class="container-custom my-5">
    <h1 class="mb-4"><i class="bi bi-heart-fill text-danger"></i> My Wishlist</h1>
    
    <?php if (empty($wishlistItems)): ?>
        <div class="text-center py-5">
            <i class="bi bi-heart" style="font-size: 5rem; color: #ccc;"></i>
            <h3 class="mt-4">Your wishlist is empty</h3>
            <p class="text-muted">Save your favorite products to view them here</p>
            <a href="/shop.php" class="btn btn-primary mt-3">
                <i class="bi bi-shop"></i> Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($wishlistItems as $product): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="<?php echo $product['image'] ?? '/assets/images/placeholder.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                    onclick="removeFromWishlist(<?php echo $product['id']; ?>, this)">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/product/<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($product['short_description'], 0, 100)); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="text-primary mb-0">$<?php echo number_format($product['price'], 2); ?></h4>
                                <button class="btn btn-primary" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        onclick="addToCartFromWishlist(<?php echo $product['id']; ?>)">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>

<script>
function removeFromWishlist(productId, btn) {
    if (!confirm('Remove this item from wishlist?')) return;
    
    fetch('/api/wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'remove',
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove card from DOM
            const card = btn.closest('.col-md-6');
            card.remove();
            
            // Check if wishlist is empty
            if (document.querySelectorAll('.col-md-6').length === 0) {
                location.reload();
            }
            
            window.WoodMart.showNotification('Removed from wishlist', 'info');
        }
    })
    .catch(error => console.error('Error:', error));
}

function addToCartFromWishlist(productId) {
    fetch('/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'add',
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.WoodMart.showNotification('Added to cart!', 'success');
        } else {
            window.WoodMart.showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        window.WoodMart.showNotification('Failed to add to cart', 'error');
    });
}
</script>

</body>
</html>
