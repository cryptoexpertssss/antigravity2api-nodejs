<?php
/**
 * Product Card V5: Fashion Swatch Style
 * Shows color circles below image; clicking color updates image via JS
 */
?>

<div class="product-card card-v5" data-product-id="<?php echo $product['id']; ?>">
    <div class="fashion-card-wrapper">
        <!-- Image Section -->
        <div class="fashion-image-container">
            <?php if (!empty($product['is_on_sale'])): ?>
                <span class="sale-tag">-<?php 
                    $discount = round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100);
                    echo $discount;
                ?>%</span>
            <?php endif; ?>
            
            <?php if (!empty($product['is_new_arrival'])): ?>
                <span class="new-tag">NEW</span>
            <?php endif; ?>
            
            <a href="/product/<?php echo $product['slug']; ?>" class="fashion-image-link">
                <img src="<?php echo $product['image'] ?? '/assets/images/placeholder-fashion.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="fashion-image main-image" 
                     data-image-main="<?php echo $product['image']; ?>"
                     loading="lazy">
                
                <?php if (!empty($product['hover_image'])): ?>
                    <img src="<?php echo $product['hover_image']; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="fashion-image hover-image" 
                         loading="lazy">
                <?php endif; ?>
            </a>
            
            <!-- Quick Actions -->
            <div class="quick-actions-fashion">
                <button class="action-btn-fashion" title="Quick View" data-bs-toggle="modal" data-bs-target="#quickViewModal">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="action-btn-fashion" title="Add to Wishlist" data-action="wishlist">
                    <i class="bi bi-heart"></i>
                </button>
                <button class="action-btn-fashion" title="Compare" data-action="compare">
                    <i class="bi bi-shuffle"></i>
                </button>
            </div>
            
            <!-- Size Guide -->
            <button class="size-guide-btn" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                <i class="bi bi-rulers"></i> Size Guide
            </button>
        </div>
        
        <!-- Product Info -->
        <div class="fashion-info">
            <?php if (!empty($product['category_name'])): ?>
                <a href="/category/<?php echo $product['category_slug']; ?>" class="fashion-category">
                    <?php echo strtoupper($product['category_name']); ?>
                </a>
            <?php endif; ?>
            
            <h3 class="fashion-title">
                <a href="/product/<?php echo $product['slug']; ?>">
                    <?php echo htmlspecialchars($product['name']); ?>
                </a>
            </h3>
            
            <?php if (!empty($product['rating_avg'])): ?>
                <div class="fashion-rating">
                    <?php 
                    $rating = $product['rating_avg'];
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                    }
                    ?>
                    <span>(<?php echo $product['rating_count'] ?? 0; ?>)</span>
                </div>
            <?php endif; ?>
            
            <!-- Color Swatches -->
            <?php 
            // Get available colors from variations
            $colors = [];
            if (!empty($product['variations'])) {
                foreach ($product['variations'] as $variation) {
                    $attrs = is_string($variation['attributes']) 
                        ? json_decode($variation['attributes'], true) 
                        : $variation['attributes'];
                    if (!empty($attrs['color'])) {
                        $colors[] = [
                            'name' => $attrs['color'],
                            'hex' => $variation['color_hex'] ?? null,
                            'image' => $variation['image'] ?? $product['image']
                        ];
                    }
                }
            }
            
            if (!empty($colors)): ?>
                <div class="color-swatches" data-product-id="<?php echo $product['id']; ?>">
                    <label class="swatch-label">Colors:</label>
                    <div class="swatch-options">
                        <?php foreach ($colors as $index => $color): ?>
                            <button class="swatch-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-color="<?php echo htmlspecialchars($color['name']); ?>"
                                    data-image="<?php echo $color['image']; ?>"
                                    style="background-color: <?php echo $color['hex'] ?? getColorHex($color['name']); ?>;"
                                    title="<?php echo htmlspecialchars($color['name']); ?>">
                            </button>
                        <?php endforeach; ?>
                        <?php if (count($colors) > 5): ?>
                            <span class="more-colors">+<?php echo count($colors) - 5; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Size Options -->
            <?php 
            $sizes = [];
            if (!empty($product['variations'])) {
                foreach ($product['variations'] as $variation) {
                    $attrs = is_string($variation['attributes']) 
                        ? json_decode($variation['attributes'], true) 
                        : $variation['attributes'];
                    if (!empty($attrs['size']) && !in_array($attrs['size'], $sizes)) {
                        $sizes[] = $attrs['size'];
                    }
                }
            }
            
            if (!empty($sizes)): ?>
                <div class="size-options">
                    <label class="size-label">Sizes:</label>
                    <div class="size-buttons">
                        <?php foreach ($sizes as $size): ?>
                            <button class="size-btn" data-size="<?php echo $size; ?>">
                                <?php echo $size; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Price -->
            <div class="fashion-price-row">
                <div class="fashion-price">
                    <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                        <span class="old-price">$<?php echo number_format($product['compare_price'], 2); ?></span>
                    <?php endif; ?>
                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                </div>
                
                <?php 
                $fashionInfo = !empty($product['json_attributes']) 
                    ? (is_string($product['json_attributes']) ? json_decode($product['json_attributes'], true) : $product['json_attributes']) 
                    : [];
                
                if (!empty($fashionInfo['material'])): ?>
                    <span class="material-tag">
                        <i class="bi bi-patch-check"></i> <?php echo $fashionInfo['material']; ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Add to Cart Button -->
            <button class="btn-add-cart-fashion" data-product-id="<?php echo $product['id']; ?>">
                <i class="bi bi-bag-plus"></i> Add to Bag
            </button>
        </div>
    </div>
</div>

<?php
// Helper function to get color hex codes
function getColorHex($colorName) {
    $colorMap = [
        'Red' => '#FF0000',
        'Blue' => '#0000FF',
        'Green' => '#00FF00',
        'Black' => '#000000',
        'White' => '#FFFFFF',
        'Yellow' => '#FFFF00',
        'Pink' => '#FFC0CB',
        'Purple' => '#800080',
        'Orange' => '#FFA500',
        'Gray' => '#808080',
        'Brown' => '#A52A2A',
        'Navy' => '#000080',
        'Beige' => '#F5F5DC',
    ];
    
    return $colorMap[$colorName] ?? '#CCCCCC';
}
?>

<style>
.card-v5 {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition-normal);
    border: 1px solid transparent;
}

.card-v5:hover {
    box-shadow: var(--shadow-lg);
    border-color: #e5e5e5;
}

.card-v5 .fashion-image-container {
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
    padding-top: 133.33%; /* 3:4 Fashion aspect ratio */
}

.card-v5 .fashion-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity var(--transition-normal);
}

.card-v5 .main-image {
    opacity: 1;
    z-index: 1;
}

.card-v5 .hover-image {
    opacity: 0;
    z-index: 2;
}

.card-v5:hover .hover-image {
    opacity: 1;
}

.card-v5 .sale-tag,
.card-v5 .new-tag {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 20px;
    z-index: 3;
}

.card-v5 .sale-tag {
    background: #FF3B3B;
    color: white;
}

.card-v5 .new-tag {
    background: #00C853;
    color: white;
    left: auto;
    right: 15px;
}

.card-v5 .quick-actions-fashion {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    gap: 0.75rem;
    opacity: 0;
    transition: opacity var(--transition-normal);
    z-index: 4;
}

.card-v5:hover .quick-actions-fashion {
    opacity: 1;
}

.card-v5 .action-btn-fashion {
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

.card-v5 .action-btn-fashion:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.15);
}

.card-v5 .size-guide-btn {
    position: absolute;
    bottom: 15px;
    right: 15px;
    padding: 0.5rem 1rem;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    opacity: 0;
    transition: all var(--transition-normal);
    z-index: 3;
    backdrop-filter: blur(5px);
}

.card-v5:hover .size-guide-btn {
    opacity: 1;
}

.card-v5 .size-guide-btn:hover {
    background: var(--primary-color);
}

.card-v5 .fashion-info {
    padding: 1.25rem;
}

.card-v5 .fashion-category {
    display: inline-block;
    font-size: 0.7rem;
    color: #999;
    text-decoration: none;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.card-v5 .fashion-category:hover {
    color: var(--primary-color);
}

.card-v5 .fashion-title {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    line-height: 1.4;
    height: 2.8rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.card-v5 .fashion-title a {
    color: var(--text-color);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.card-v5 .fashion-title a:hover {
    color: var(--primary-color);
}

.card-v5 .fashion-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
}

.card-v5 .fashion-rating i {
    color: #ffc107;
}

.card-v5 .fashion-rating span {
    color: #999;
    font-size: 0.75rem;
    margin-left: 0.25rem;
}

/* Color Swatches */
.card-v5 .color-swatches {
    margin-bottom: 1rem;
}

.card-v5 .swatch-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #666;
    margin-bottom: 0.5rem;
}

.card-v5 .swatch-options {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.card-v5 .swatch-item {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
}

.card-v5 .swatch-item:hover,
.card-v5 .swatch-item.active {
    border-color: var(--text-color);
    transform: scale(1.15);
}

.card-v5 .swatch-item.active::after {
    content: '\2713';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 0.8rem;
    font-weight: bold;
    text-shadow: 0 0 3px rgba(0,0,0,0.5);
}

.card-v5 .more-colors {
    font-size: 0.75rem;
    color: var(--primary-color);
    font-weight: 600;
}

/* Size Options */
.card-v5 .size-options {
    margin-bottom: 1rem;
}

.card-v5 .size-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #666;
    margin-bottom: 0.5rem;
}

.card-v5 .size-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.card-v5 .size-btn {
    min-width: 40px;
    padding: 0.4rem 0.75rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v5 .size-btn:hover,
.card-v5 .size-btn.active {
    background: var(--text-color);
    color: white;
    border-color: var(--text-color);
}

/* Price */
.card-v5 .fashion-price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.card-v5 .fashion-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-v5 .old-price {
    font-size: 0.9rem;
    color: #999;
    text-decoration: line-through;
}

.card-v5 .current-price {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-color);
}

.card-v5 .material-tag {
    font-size: 0.75rem;
    color: #28a745;
    background: #e8f5e9;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 600;
}

/* Add to Cart */
.card-v5 .btn-add-cart-fashion {
    width: 100%;
    padding: 0.875rem;
    background: var(--text-color);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.card-v5 .btn-add-cart-fashion:hover {
    background: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
</style>

<script>
// Color Swatch Functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.card-v5 .swatch-item').forEach(swatch => {
        swatch.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from siblings
            this.closest('.swatch-options').querySelectorAll('.swatch-item').forEach(s => {
                s.classList.remove('active');
            });
            
            // Add active class to clicked swatch
            this.classList.add('active');
            
            // Update main image
            const newImage = this.dataset.image;
            const card = this.closest('.card-v5');
            const mainImg = card.querySelector('.main-image');
            
            if (newImage && mainImg) {
                mainImg.style.opacity = 0;
                setTimeout(() => {
                    mainImg.src = newImage;
                    mainImg.style.opacity = 1;
                }, 200);
            }
        });
    });
    
    // Size Selection
    document.querySelectorAll('.card-v5 .size-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from siblings
            this.closest('.size-buttons').querySelectorAll('.size-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
        });
    });
});
</script>
