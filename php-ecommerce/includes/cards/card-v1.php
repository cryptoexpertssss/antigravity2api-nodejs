<?php
/**
 * Product Card V1: WoodMart Standard
 * Quick view button slides up on hover, Add to cart icon
 * 
 * Usage: include 'includes/cards/card-v1.php';
 * Pass $product array with keys: id, name, slug, price, compare_price, image, rating, is_new_arrival, is_on_sale
 */
?>

<div class="product-card card-v1" data-product-id="<?php echo $product['id']; ?>">
    <div class="card-image-wrapper">
        <?php if (!empty($product['is_on_sale'])): ?>
            <span class="badge-sale">SALE</span>
        <?php endif; ?>
        <?php if (!empty($product['is_new_arrival'])): ?>
            <span class="badge-new">NEW</span>
        <?php endif; ?>
        
        <a href="/product/<?php echo $product['slug']; ?>" class="product-image-link">
            <img src="<?php echo $product['image'] ?? '/assets/images/placeholder.jpg'; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="product-image" 
                 loading="lazy">
        </a>
        
        <!-- Hover Actions -->
        <div class="hover-actions">
            <button class="btn-quick-view" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="<?php echo $product['id']; ?>">
                <i class="bi bi-eye"></i> Quick View
            </button>
        </div>
        
        <!-- Icon Actions -->
        <div class="icon-actions">
            <button class="action-icon btn-wishlist" title="Add to Wishlist" data-product-id="<?php echo $product['id']; ?>">
                <i class="bi bi-heart"></i>
            </button>
            <button class="action-icon btn-compare" title="Compare" data-product-id="<?php echo $product['id']; ?>">
                <i class="bi bi-arrow-left-right"></i>
            </button>
        </div>
    </div>
    
    <div class="card-body-v1">
        <?php if (!empty($product['category_name'])): ?>
            <a href="/category/<?php echo $product['category_slug']; ?>" class="product-category">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
        <?php endif; ?>
        
        <h3 class="product-title">
            <a href="/product/<?php echo $product['slug']; ?>">
                <?php echo htmlspecialchars($product['name']); ?>
            </a>
        </h3>
        
        <?php if (!empty($product['rating_avg'])): ?>
            <div class="product-rating">
                <?php 
                $rating = $product['rating_avg'];
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                }
                ?>
                <span class="rating-count">(<?php echo $product['rating_count'] ?? 0; ?>)</span>
            </div>
        <?php endif; ?>
        
        <div class="product-price">
            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                <span class="price-old">$<?php echo number_format($product['compare_price'], 2); ?></span>
            <?php endif; ?>
            <span class="price-current">$<?php echo number_format($product['price'], 2); ?></span>
            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']):
                $discount = round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100);
            ?>
                <span class="price-discount">-<?php echo $discount; ?>%</span>
            <?php endif; ?>
        </div>
        
        <button class="btn-add-to-cart" data-product-id="<?php echo $product['id']; ?>">
            <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
    </div>
</div>

<style>
.card-v1 {
    border: 1px solid #e5e5e5;
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition-normal);
    background: white;
    position: relative;
}

.card-v1:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
    border-color: var(--primary-color);
}

.card-v1 .card-image-wrapper {
    position: relative;
    overflow: hidden;
    padding-top: 100%; /* 1:1 Aspect Ratio */
    background: #f5f5f5;
}

.card-v1 .product-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.card-v1:hover .product-image {
    transform: scale(1.1);
}

.card-v1 .badge-sale,
.card-v1 .badge-new {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 2;
}

.card-v1 .badge-sale {
    background: var(--primary-color);
    color: white;
}

.card-v1 .badge-new {
    background: var(--secondary-color);
    color: white;
    top: 40px;
}

.card-v1 .hover-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    transform: translateY(100%);
    transition: transform var(--transition-normal);
    z-index: 3;
}

.card-v1:hover .hover-actions {
    transform: translateY(0);
}

.card-v1 .btn-quick-view {
    width: 100%;
    padding: 0.75rem;
    background: var(--primary-color);
    color: white;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.card-v1 .btn-quick-view:hover {
    background: var(--primary-hover);
}

.card-v1 .icon-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    opacity: 0;
    transform: translateX(20px);
    transition: all var(--transition-normal);
    z-index: 2;
}

.card-v1:hover .icon-actions {
    opacity: 1;
    transform: translateX(0);
}

.card-v1 .action-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #e5e5e5;
    border-radius: 50%;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v1 .action-icon:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: scale(1.1);
}

.card-v1 .card-body-v1 {
    padding: 1rem;
}

.card-v1 .product-category {
    display: block;
    font-size: 0.75rem;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    text-decoration: none;
}

.card-v1 .product-category:hover {
    color: var(--primary-color);
}

.card-v1 .product-title {
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
    line-height: 1.4;
    height: 2.8rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.card-v1 .product-title a {
    color: var(--text-color);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.card-v1 .product-title a:hover {
    color: var(--primary-color);
}

.card-v1 .product-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
}

.card-v1 .product-rating i {
    color: #ffc107;
}

.card-v1 .rating-count {
    color: #999;
    font-size: 0.8rem;
    margin-left: 0.25rem;
}

.card-v1 .product-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.card-v1 .price-old {
    color: #999;
    text-decoration: line-through;
    font-size: 0.9rem;
}

.card-v1 .price-current {
    color: var(--primary-color);
    font-size: 1.25rem;
    font-weight: 700;
}

.card-v1 .price-discount {
    background: #ff4444;
    color: white;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.card-v1 .btn-add-to-cart {
    width: 100%;
    padding: 0.75rem;
    background: var(--text-color);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v1 .btn-add-to-cart:hover {
    background: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
</style>
