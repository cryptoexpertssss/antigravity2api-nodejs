<?php
/**
 * Product Card V2: Minimal
 * Clean image, text below, no borders
 */
?>

<div class="product-card card-v2" data-product-id="<?php echo $product['id']; ?>">
    <div class="card-image-minimal">
        <?php if (!empty($product['is_on_sale'])): ?>
            <span class="tag-minimal tag-sale">SALE</span>
        <?php endif; ?>
        
        <a href="/product/<?php echo $product['slug']; ?>" class="image-link-minimal">
            <img src="<?php echo $product['image'] ?? '/assets/images/placeholder.jpg'; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="img-minimal" 
                 loading="lazy">
        </a>
        
        <div class="actions-minimal">
            <button class="btn-icon-minimal" title="Wishlist" data-action="wishlist">
                <i class="bi bi-heart"></i>
            </button>
            <button class="btn-icon-minimal" title="Quick View" data-bs-toggle="modal" data-bs-target="#quickViewModal">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="card-content-minimal">
        <a href="/product/<?php echo $product['slug']; ?>" class="title-minimal">
            <?php echo htmlspecialchars($product['name']); ?>
        </a>
        
        <?php if (!empty($product['short_description'])): ?>
            <p class="desc-minimal"><?php echo htmlspecialchars(substr($product['short_description'], 0, 60)); ?>...</p>
        <?php endif; ?>
        
        <div class="price-minimal">
            <span class="current-minimal">$<?php echo number_format($product['price'], 2); ?></span>
            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                <span class="old-minimal">$<?php echo number_format($product['compare_price'], 2); ?></span>
            <?php endif; ?>
        </div>
        
        <button class="btn-cart-minimal" data-product-id="<?php echo $product['id']; ?>">
            Add to Cart
        </button>
    </div>
</div>

<style>
.card-v2 {
    background: transparent;
    border: none;
    transition: transform var(--transition-normal);
}

.card-v2:hover {
    transform: translateY(-3px);
}

.card-v2 .card-image-minimal {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-lg);
    background: #fafafa;
    margin-bottom: 1rem;
    padding-top: 120%; /* Taller aspect ratio */
}

.card-v2 .img-minimal {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity var(--transition-normal);
}

.card-v2:hover .img-minimal {
    opacity: 0.9;
}

.card-v2 .tag-minimal {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 0.4rem 0.8rem;
    background: white;
    color: var(--text-color);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 3px;
    z-index: 2;
}

.card-v2 .tag-sale {
    background: var(--primary-color);
    color: white;
}

.card-v2 .actions-minimal {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    gap: 0.75rem;
    opacity: 0;
    transition: opacity var(--transition-normal);
    z-index: 3;
}

.card-v2:hover .actions-minimal {
    opacity: 1;
}

.card-v2 .btn-icon-minimal {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: none;
    border-radius: 50%;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    box-shadow: var(--shadow-md);
}

.card-v2 .btn-icon-minimal:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.15);
}

.card-v2 .card-content-minimal {
    text-align: center;
}

.card-v2 .title-minimal {
    display: block;
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-color);
    text-decoration: none;
    margin-bottom: 0.5rem;
    transition: color var(--transition-fast);
    line-height: 1.4;
}

.card-v2 .title-minimal:hover {
    color: var(--primary-color);
}

.card-v2 .desc-minimal {
    font-size: 0.85rem;
    color: #777;
    margin-bottom: 0.75rem;
    line-height: 1.5;
}

.card-v2 .price-minimal {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.card-v2 .current-minimal {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-color);
}

.card-v2 .old-minimal {
    font-size: 1rem;
    color: #999;
    text-decoration: line-through;
}

.card-v2 .btn-cart-minimal {
    width: 100%;
    padding: 0.75rem;
    background: transparent;
    color: var(--text-color);
    border: 2px solid var(--text-color);
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v2 .btn-cart-minimal:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}
</style>
