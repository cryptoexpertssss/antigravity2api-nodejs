<?php
/**
 * Single Product Page
 * Advanced product display with type detection
 */

require_once dirname(__DIR__) . '/app/helpers/Database.php';
require_once dirname(__DIR__) . '/app/helpers/ThemeHelper.php';
require_once dirname(__DIR__) . '/app/controllers/ProductController.php';

// Get product slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /shop.php');
    exit();
}

// Initialize controller
$productController = new ProductController();

// Get product
$product = $productController->getProductBySlug($slug);

if (!$product) {
    header('HTTP/1.0 404 Not Found');
    echo '404 - Product Not Found';
    exit();
}

// Detect product type
$productType = $product['product_type'];
$attributes = $product['json_attributes'] ?? [];
$variations = $product['variations'] ?? [];
$images = $product['images'] ?? [];

// Get related products
$relatedProducts = $productController->getRelatedProducts($product['id'], $product['category_id'], 4);

// Header layout
$headerLayout = ThemeHelper::getHeaderLayout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - WoodMart</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/dynamic-style.php">
    
    <style>
        .product-gallery img {
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .thumbnail-gallery {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .thumbnail-gallery img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .thumbnail-gallery img:hover,
        .thumbnail-gallery img.active {
            border-color: var(--primary-color);
        }
        
        .color-swatch, .size-swatch {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 45px;
            height: 45px;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-right: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            padding: 0.5rem;
        }
        
        .color-swatch {
            width: 45px;
            border-radius: 50%;
        }
        
        .color-swatch:hover,
        .size-swatch:hover,
        .color-swatch.active,
        .size-swatch.active {
            border-color: var(--primary-color);
            transform: scale(1.1);
        }
        
        .product-tabs .nav-link {
            border-radius: 0;
        }
        
        .product-tabs .nav-link.active {
            border-bottom: 3px solid var(--primary-color);
        }
    </style>
</head>
<body>

<?php include dirname(__DIR__) . "/includes/headers/header-v{$headerLayout}.php"; ?>

<div class="container-custom my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/shop.php">Shop</a></li>
            <li class="breadcrumb-item"><a href="/category/<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Gallery -->
        <div class="col-lg-6">
            <div class="product-gallery">
                <img src="<?php echo $images[0]['image_path'] ?? $product['image'] ?? '/assets/images/placeholder.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     id="productMainImage"
                     class="img-fluid">
            </div>
            
            <?php if (count($images) > 1): ?>
                <div class="thumbnail-gallery">
                    <?php foreach ($images as $index => $image): ?>
                        <img src="<?php echo $image['image_path']; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="<?php echo $index === 0 ? 'active' : ''; ?>"
                             onclick="changeMainImage(this)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <?php if ($product['rating_avg'] > 0): ?>
                <div class="mb-3">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?php echo $i <= $product['rating_avg'] ? '-fill' : ''; ?>" style="color: #ffc107;"></i>
                    <?php endfor; ?>
                    <span class="ms-2"><?php echo $product['rating_avg']; ?> (<?php echo $product['rating_count']; ?> reviews)</span>
                </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                    <span class="h4 text-muted text-decoration-line-through me-2">$<?php echo number_format($product['compare_price'], 2); ?></span>
                <?php endif; ?>
                <span class="h2 text-primary" id="productPrice">$<?php echo number_format($product['price'], 2); ?></span>
                
                <?php if ($product['is_on_sale']): ?>
                    <span class="badge bg-danger ms-2">
                        SAVE <?php echo round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100); ?>%
                    </span>
                <?php endif; ?>
            </div>
            
            <p class="lead"><?php echo htmlspecialchars($product['short_description']); ?></p>
            
            <!-- Product Type Specific Sections -->
            
            <?php if ($productType === 'fashion' && !empty($variations)): ?>
                <!-- Fashion: Color & Size Swatches -->
                <?php
                $colors = [];
                $sizes = [];
                foreach ($variations as $variation) {
                    if (!empty($variation['attributes']['color']) && !in_array($variation['attributes']['color'], array_column($colors, 'name'))) {
                        $colors[] = [
                            'name' => $variation['attributes']['color'],
                            'variation_id' => $variation['id'],
                            'price' => $variation['price'] ?? $product['price'],
                            'stock' => $variation['stock_quantity'],
                            'image' => $variation['image'] ?? ($images[0]['image_path'] ?? '')
                        ];
                    }
                    if (!empty($variation['attributes']['size']) && !in_array($variation['attributes']['size'], $sizes)) {
                        $sizes[] = $variation['attributes']['size'];
                    }
                }
                ?>
                
                <?php if (!empty($colors)): ?>
                    <div class="mb-4">
                        <h6>Select Color:</h6>
                        <div class="color-swatches">
                            <?php foreach ($colors as $index => $color): ?>
                                <button class="color-swatch <?php echo $index === 0 ? 'active' : ''; ?>"
                                        style="background-color: <?php echo getColorHex($color['name']); ?>;"
                                        data-variation-id="<?php echo $color['variation_id']; ?>"
                                        data-price="<?php echo $color['price']; ?>"
                                        data-stock="<?php echo $color['stock']; ?>"
                                        data-image="<?php echo $color['image']; ?>"
                                        title="<?php echo $color['name']; ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($sizes)): ?>
                    <div class="mb-4">
                        <h6>Select Size:</h6>
                        <div class="size-swatches">
                            <?php foreach ($sizes as $index => $size): ?>
                                <button class="size-swatch <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <?php echo $size; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php elseif ($productType === 'books'): ?>
                <!-- Books: Author Info -->
                <?php if (!empty($attributes['author'])): ?>
                    <div class="alert alert-info mb-4">
                        <strong><i class="bi bi-person-circle"></i> Author:</strong> <?php echo htmlspecialchars($attributes['author']); ?>
                        <?php if (!empty($attributes['publisher'])): ?>
                            <br><strong><i class="bi bi-building"></i> Publisher:</strong> <?php echo htmlspecialchars($attributes['publisher']); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php elseif ($productType === 'electronics'): ?>
                <!-- Electronics: Key Specs -->
                <?php if (!empty($attributes)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong><i class="bi bi-cpu"></i> Key Specifications</strong>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($attributes, 0, 5) as $key => $value): ?>
                                <li class="list-group-item">
                                    <strong><?php echo ucfirst($key); ?>:</strong> 
                                    <?php echo is_array($value) ? json_encode($value) : $value; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Stock Status -->
            <div class="mb-4">
                <span id="productStock" class="<?php echo $product['stock_quantity'] > 0 ? 'text-success' : 'text-danger'; ?>">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <i class="bi bi-check-circle"></i> <?php echo $product['stock_quantity']; ?> in stock
                    <?php else: ?>
                        <i class="bi bi-x-circle"></i> Out of stock
                    <?php endif; ?>
                </span>
            </div>
            
            <!-- Quantity & Add to Cart -->
            <div class="d-flex gap-3 mb-4">
                <div class="input-group" style="max-width: 150px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">-</button>
                    <input type="number" class="form-control text-center" id="productQuantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">+</button>
                </div>
                
                <button class="btn btn-primary btn-lg flex-grow-1" 
                        data-product-add-to-cart
                        data-product-id="<?php echo $product['id']; ?>"
                        onclick="addToCartFromProduct()">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
            </div>
            
            <!-- Wishlist & Compare -->
            <div class="d-flex gap-2 mb-4">
                <button class="btn btn-outline-secondary">
                    <i class="bi bi-heart"></i> Add to Wishlist
                </button>
                <button class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-right"></i> Compare
                </button>
            </div>
            
            <!-- Additional Info -->
            <div class="border-top pt-3">
                <p class="mb-2"><strong>SKU:</strong> <?php echo $product['sku']; ?></p>
                <p class="mb-2"><strong>Category:</strong> <a href="/category/<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a></p>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs product-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button>
                </li>
                <?php if ($productType === 'books'): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sample">Read Sample</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#author">Author Bio</button>
                    </li>
                <?php elseif ($productType === 'electronics'): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specifications">Full Specifications</button>
                    </li>
                <?php endif; ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews (<?php echo $product['rating_count']; ?>)</button>
                </li>
            </ul>
            
            <div class="tab-content p-4 border border-top-0">
                <div class="tab-pane fade show active" id="description">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <?php if ($productType === 'books'): ?>
                    <div class="tab-pane fade" id="sample">
                        <p class="lead">Read a sample chapter...</p>
                        <p>Sample content would go here. This could be loaded dynamically or stored in the database.</p>
                    </div>
                    <div class="tab-pane fade" id="author">
                        <?php if (!empty($attributes['author'])): ?>
                            <h5><?php echo htmlspecialchars($attributes['author']); ?></h5>
                            <p>Author biography and information would appear here.</p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($productType === 'electronics'): ?>
                    <div class="tab-pane fade" id="specifications">
                        <table class="table table-striped">
                            <?php foreach ($attributes as $key => $value): ?>
                                <tr>
                                    <th width="30%"><?php echo ucfirst($key); ?></th>
                                    <td><?php echo is_array($value) ? json_encode($value) : $value; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
                
                <div class="tab-pane fade" id="reviews">
                    <h5>Customer Reviews</h5>
                    <?php if (!empty($product['reviews'])): ?>
                        <?php foreach ($product['reviews'] as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($review['first_name']); ?></strong>
                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?php echo $i <= $review['rating'] ? '-fill' : ''; ?>" style="color: #ffc107;"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php if ($review['title']): ?>
                                    <h6><?php echo htmlspecialchars($review['title']); ?></h6>
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet. Be the first to review this product!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Mini Cart -->
<?php include 'cart-mini.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>

<script>
function changeMainImage(thumb) {
    document.getElementById('productMainImage').src = thumb.src;
    document.querySelectorAll('.thumbnail-gallery img').forEach(img => img.classList.remove('active'));
    thumb.classList.add('active');
}

function decreaseQty() {
    const input = document.getElementById('productQuantity');
    if (input.value > 1) input.value--;
}

function increaseQty() {
    const input = document.getElementById('productQuantity');
    const max = parseInt(input.max);
    if (input.value < max) input.value++;
}

function addToCartFromProduct() {
    const productId = document.querySelector('[data-product-add-to-cart]').dataset.productId;
    const variationId = document.querySelector('[data-product-add-to-cart]').dataset.variationId || null;
    const quantity = document.getElementById('productQuantity').value;
    
    const btn = document.querySelector('[data-product-add-to-cart]');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Adding...';
    btn.disabled = true;
    
    fetch('/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'add',
            product_id: productId,
            variation_id: variationId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="bi bi-check"></i> Added!';
            window.WoodMart.showNotification('Product added to cart!', 'success');
            
            // Open mini cart (if exists)
            if (typeof openMiniCart === 'function') {
                setTimeout(() => openMiniCart(), 500);
            }
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }, 2000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        window.WoodMart.showNotification(error.message, 'error');
    });
}

<?php
// Helper function for color hex
function getColorHex($colorName) {
    $colorMap = [
        'Red' => '#FF0000', 'Blue' => '#0000FF', 'Green' => '#00FF00',
        'Black' => '#000000', 'White' => '#FFFFFF', 'Yellow' => '#FFFF00',
        'Pink' => '#FFC0CB', 'Purple' => '#800080', 'Orange' => '#FFA500',
        'Gray' => '#808080', 'Brown' => '#A52A2A'
    ];
    return $colorMap[$colorName] ?? '#CCCCCC';
}
?>
</script>

</body>
</html>
