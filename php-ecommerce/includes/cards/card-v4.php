<?php
/**
 * Product Card V4: Book Store Style
 * 3D shadow effect looking like a physical book
 */
?>

<div class="product-card card-v4" data-product-id="<?php echo $product['id']; ?>">
    <div class="book-wrapper">
        <div class="book-cover">
            <?php if (!empty($product['is_new_arrival'])): ?>
                <div class="ribbon-new">NEW</div>
            <?php endif; ?>
            <?php if (!empty($product['is_on_sale'])): ?>
                <div class="ribbon-sale">SALE</div>
            <?php endif; ?>
            
            <a href="/product/<?php echo $product['slug']; ?>" class="book-link">
                <img src="<?php echo $product['image'] ?? '/assets/images/placeholder-book.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="book-image" 
                     loading="lazy">
                <div class="book-spine"></div>
            </a>
            
            <!-- Book Details Overlay -->
            <div class="book-overlay">
                <div class="overlay-content">
                    <?php 
                    $bookInfo = !empty($product['json_attributes']) 
                        ? (is_string($product['json_attributes']) ? json_decode($product['json_attributes'], true) : $product['json_attributes']) 
                        : [];
                    ?>
                    <?php if (!empty($bookInfo['author'])): ?>
                        <p class="book-author"><i class="bi bi-person"></i> <?php echo $bookInfo['author']; ?></p>
                    <?php endif; ?>
                    <?php if (!empty($bookInfo['publisher'])): ?>
                        <p class="book-publisher"><i class="bi bi-building"></i> <?php echo $bookInfo['publisher']; ?></p>
                    <?php endif; ?>
                    <?php if (!empty($bookInfo['pages'])): ?>
                        <p class="book-pages"><i class="bi bi-file-text"></i> <?php echo $bookInfo['pages']; ?> pages</p>
                    <?php endif; ?>
                    <?php if (!empty($bookInfo['isbn'])): ?>
                        <p class="book-isbn"><i class="bi bi-upc"></i> <?php echo $bookInfo['isbn']; ?></p>
                    <?php endif; ?>
                    <button class="btn-preview" data-bs-toggle="modal" data-bs-target="#previewModal">
                        <i class="bi bi-eye"></i> Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="book-info">
        <h3 class="book-title">
            <a href="/product/<?php echo $product['slug']; ?>">
                <?php echo htmlspecialchars($product['name']); ?>
            </a>
        </h3>
        
        <?php if (!empty($bookInfo['author'])): ?>
            <p class="author-name">by <?php echo htmlspecialchars($bookInfo['author']); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($product['rating_avg'])): ?>
            <div class="book-rating">
                <?php 
                $rating = $product['rating_avg'];
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                }
                ?>
                <span class="rating-text"><?php echo number_format($rating, 1); ?> (<?php echo $product['rating_count'] ?? 0; ?>)</span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($product['short_description'])): ?>
            <p class="book-description"><?php echo htmlspecialchars(substr($product['short_description'], 0, 100)); ?>...</p>
        <?php endif; ?>
        
        <div class="book-footer">
            <div class="book-price">
                <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                    <span class="price-strike">$<?php echo number_format($product['compare_price'], 2); ?></span>
                <?php endif; ?>
                <span class="price-main">$<?php echo number_format($product['price'], 2); ?></span>
            </div>
            
            <div class="book-actions">
                <button class="btn-add-cart-book" data-product-id="<?php echo $product['id']; ?>">
                    <i class="bi bi-cart-plus"></i>
                </button>
                <button class="btn-wishlist-book" data-product-id="<?php echo $product['id']; ?>">
                    <i class="bi bi-heart"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.card-v4 {
    padding: 1rem;
    background: transparent;
}

.card-v4 .book-wrapper {
    perspective: 1500px;
    margin-bottom: 1.5rem;
}

.card-v4 .book-cover {
    position: relative;
    width: 100%;
    padding-top: 150%; /* 2:3 Book aspect ratio */
    transform-style: preserve-3d;
    transition: transform var(--transition-slow);
    cursor: pointer;
}

.card-v4:hover .book-cover {
    transform: rotateY(-15deg) rotateX(5deg);
}

.card-v4 .book-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px 8px 8px 4px;
    box-shadow: 
        5px 5px 10px rgba(0,0,0,0.2),
        10px 10px 20px rgba(0,0,0,0.15),
        15px 15px 30px rgba(0,0,0,0.1);
    transition: all var(--transition-slow);
}

.card-v4:hover .book-image {
    box-shadow: 
        8px 8px 15px rgba(0,0,0,0.25),
        15px 15px 30px rgba(0,0,0,0.2),
        25px 25px 50px rgba(0,0,0,0.15);
}

.card-v4 .book-spine {
    position: absolute;
    top: 0;
    left: -10px;
    width: 10px;
    height: 100%;
    background: linear-gradient(to right, rgba(0,0,0,0.4), rgba(0,0,0,0.1));
    border-radius: 4px 0 0 4px;
    transform: rotateY(-90deg);
    transform-origin: right;
}

.card-v4 .ribbon-new,
.card-v4 .ribbon-sale {
    position: absolute;
    top: 20px;
    right: -5px;
    padding: 0.4rem 0.8rem;
    background: var(--secondary-color);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1px;
    z-index: 2;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
}

.card-v4 .ribbon-new::before,
.card-v4 .ribbon-sale::before {
    content: '';
    position: absolute;
    bottom: -5px;
    right: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 5px 5px 0;
    border-color: transparent rgba(0,0,0,0.3) transparent transparent;
}

.card-v4 .ribbon-sale {
    top: 55px;
    background: var(--primary-color);
}

.card-v4 .book-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    opacity: 0;
    transition: opacity var(--transition-normal);
    display: flex;
    align-items: flex-end;
    padding: 1.5rem 1rem;
    border-radius: 4px 8px 8px 4px;
    z-index: 1;
}

.card-v4:hover .book-overlay {
    opacity: 1;
}

.card-v4 .overlay-content {
    color: white;
    width: 100%;
}

.card-v4 .overlay-content p {
    font-size: 0.75rem;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-v4 .btn-preview {
    width: 100%;
    padding: 0.5rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.card-v4 .btn-preview:hover {
    background: var(--primary-hover);
}

.card-v4 .book-info {
    text-align: center;
}

.card-v4 .book-title {
    font-size: 1.05rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.4;
    height: 2.8rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.card-v4 .book-title a {
    color: var(--text-color);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.card-v4 .book-title a:hover {
    color: var(--primary-color);
}

.card-v4 .author-name {
    font-size: 0.9rem;
    color: #666;
    font-style: italic;
    margin-bottom: 0.75rem;
}

.card-v4 .book-rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
}

.card-v4 .book-rating i {
    color: #ffc107;
}

.card-v4 .rating-text {
    color: #666;
    font-size: 0.8rem;
    margin-left: 0.5rem;
}

.card-v4 .book-description {
    font-size: 0.85rem;
    color: #777;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.card-v4 .book-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e5e5e5;
}

.card-v4 .book-price {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.card-v4 .price-strike {
    font-size: 0.8rem;
    color: #999;
    text-decoration: line-through;
}

.card-v4 .price-main {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.card-v4 .book-actions {
    display: flex;
    gap: 0.5rem;
}

.card-v4 .btn-add-cart-book,
.card-v4 .btn-wishlist-book {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--text-color);
    background: transparent;
    border-radius: 50%;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.card-v4 .btn-add-cart-book:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.card-v4 .btn-wishlist-book:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
    transform: scale(1.1);
}
</style>
