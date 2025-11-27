<?php
/**
 * Product Card V3: Tech Specs (List View)
 * Perfect for Electronics with specifications
 */
?>

<div class="product-card card-v3" data-product-id="<?php echo $product['id']; ?>">
    <div class="row g-0">
        <!-- Image Column -->
        <div class="col-md-3">
            <div class="card-image-tech">
                <?php if (!empty($product['is_featured'])): ?>
                    <span class="badge-featured"><i class="bi bi-star-fill"></i> Featured</span>
                <?php endif; ?>
                <a href="/product/<?php echo $product['slug']; ?>">
                    <img src="<?php echo $product['image'] ?? '/assets/images/placeholder.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="img-tech" 
                         loading="lazy">
                </a>
            </div>
        </div>
        
        <!-- Content Column -->
        <div class="col-md-9">
            <div class="card-body-tech">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h3 class="title-tech">
                                    <a href="/product/<?php echo $product['slug']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>
                                <?php if (!empty($product['category_name'])): ?>
                                    <span class="category-tech"><?php echo $product['category_name']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="rating-tech">
                                <?php 
                                $rating = $product['rating_avg'] ?? 4;
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                }
                                ?>
                                <span>(<?php echo $product['rating_count'] ?? 0; ?>)</span>
                            </div>
                        </div>
                        
                        <?php if (!empty($product['short_description'])): ?>
                            <p class="desc-tech"><?php echo htmlspecialchars($product['short_description']); ?></p>
                        <?php endif; ?>
                        
                        <!-- Specifications -->
                        <?php if (!empty($product['json_attributes'])): 
                            $specs = is_string($product['json_attributes']) 
                                ? json_decode($product['json_attributes'], true) 
                                : $product['json_attributes'];
                        ?>
                            <div class="specs-tech">
                                <h6>Key Specifications:</h6>
                                <ul class="specs-list">
                                    <?php foreach (array_slice($specs, 0, 4) as $key => $value): ?>
                                        <li>
                                            <strong><?php echo ucfirst($key); ?>:</strong> 
                                            <?php echo is_array($value) ? json_encode($value) : $value; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Stock Status -->
                        <div class="stock-tech">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> In Stock</span>
                                <span class="text-muted ms-2"><?php echo $product['stock_quantity']; ?> units available</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Price & Actions Column -->
                    <div class="col-lg-4">
                        <div class="action-section-tech">
                            <div class="price-box-tech">
                                <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                                    <div class="price-old-tech">$<?php echo number_format($product['compare_price'], 2); ?></div>
                                    <?php 
                                    $discount = round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100);
                                    ?>
                                    <div class="price-save-tech">Save <?php echo $discount; ?>%</div>
                                <?php endif; ?>
                                <div class="price-current-tech">$<?php echo number_format($product['price'], 2); ?></div>
                            </div>
                            
                            <button class="btn-primary-tech" data-product-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                            
                            <div class="secondary-actions-tech">
                                <button class="btn-secondary-tech" data-action="wishlist">
                                    <i class="bi bi-heart"></i> Wishlist
                                </button>
                                <button class="btn-secondary-tech" data-action="compare">
                                    <i class="bi bi-arrow-left-right"></i> Compare
                                </button>
                            </div>
                            
                            <a href="/product/<?php echo $product['slug']; ?>" class="btn-details-tech">
                                View Full Specifications <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-v3 {
    background: white;
    border: 1px solid #e5e5e5;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
    transition: all var(--transition-normal);
}

.card-v3:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-lg);
}

.card-v3 .card-image-tech {
    position: relative;
    padding: 1.5rem;
    background: #f8f9fa;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-v3 .badge-featured {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ffc107;
    color: #000;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.card-v3 .img-tech {
    max-width: 100%;
    height: auto;
    max-height: 200px;
    object-fit: contain;
    transition: transform var(--transition-normal);
}

.card-v3:hover .img-tech {
    transform: scale(1.05);
}

.card-v3 .card-body-tech {
    padding: 1.5rem;
}

.card-v3 .title-tech {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.card-v3 .title-tech a {
    color: var(--text-color);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.card-v3 .title-tech a:hover {
    color: var(--primary-color);
}

.card-v3 .category-tech {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #e3f2fd;
    color: #1976d2;
    font-size: 0.75rem;
    border-radius: 12px;
    font-weight: 500;
}

.card-v3 .rating-tech {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #ffc107;
}

.card-v3 .rating-tech span {
    color: #666;
    font-size: 0.85rem;
    margin-left: 0.25rem;
}

.card-v3 .desc-tech {
    color: #666;
    line-height: 1.6;
    margin: 1rem 0;
}

.card-v3 .specs-tech h6 {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--text-color);
}

.card-v3 .specs-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.card-v3 .specs-list li {
    padding: 0.4rem 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.85rem;
    display: flex;
    justify-content: space-between;
}

.card-v3 .specs-list li:last-child {
    border-bottom: none;
}

.card-v3 .specs-list strong {
    color: #666;
    font-weight: 500;
}

.card-v3 .stock-tech {
    margin-top: 1rem;
    display: flex;
    align-items: center;
}

.card-v3 .action-section-tech {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: var(--radius-md);
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.card-v3 .price-box-tech {
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: var(--radius-md);
}

.card-v3 .price-old-tech {
    font-size: 0.9rem;
    color: #999;
    text-decoration: line-through;
}

.card-v3 .price-save-tech {
    font-size: 0.75rem;
    color: #28a745;
    font-weight: 600;
}

.card-v3 .price-current-tech {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-top: 0.25rem;
}

.card-v3 .btn-primary-tech {
    width: 100%;
    padding: 1rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v3 .btn-primary-tech:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.card-v3 .secondary-actions-tech {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.card-v3 .btn-secondary-tech {
    padding: 0.75rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: var(--radius-md);
    font-size: 0.85rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v3 .btn-secondary-tech:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.card-v3 .btn-details-tech {
    display: block;
    text-align: center;
    padding: 0.75rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: color var(--transition-fast);
}

.card-v3 .btn-details-tech:hover {
    color: var(--primary-hover);
}
</style>
