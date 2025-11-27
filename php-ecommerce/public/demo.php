<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WoodMart Clone - Visual Demo</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Dynamic Theme Styles -->
    <link rel="stylesheet" href="assets/dynamic-style.php">
    
    <style>
        .demo-section {
            padding: 3rem 0;
            border-bottom: 2px solid #e5e5e5;
        }
        .demo-heading {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary-color);
        }
        .demo-note {
            background: #f8f9fa;
            padding: 1rem;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>

<?php
// Include helpers
require_once dirname(__DIR__) . '/app/helpers/Database.php';
require_once dirname(__DIR__) . '/app/helpers/ThemeHelper.php';

// Sample product data
$sampleProducts = [
    [
        'id' => 1,
        'name' => 'Premium Wireless Headphones',
        'slug' => 'premium-wireless-headphones',
        'price' => 129.99,
        'compare_price' => 199.99,
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400',
        'hover_image' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400',
        'category_name' => 'Electronics',
        'category_slug' => 'electronics',
        'short_description' => 'High-quality wireless headphones with noise cancellation',
        'rating_avg' => 4.5,
        'rating_count' => 128,
        'is_on_sale' => true,
        'is_new_arrival' => false,
        'is_featured' => true,
        'stock_quantity' => 45,
        'json_attributes' => json_encode([
            'brand' => 'AudioTech',
            'model' => 'AT-5000',
            'warranty' => '2 Years',
            'specifications' => [
                'battery' => '30 hours',
                'bluetooth' => '5.0'
            ]
        ])
    ],
    [
        'id' => 2,
        'name' => 'The Art of Programming',
        'slug' => 'the-art-of-programming',
        'price' => 34.99,
        'compare_price' => 49.99,
        'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=400',
        'category_name' => 'Books',
        'category_slug' => 'books',
        'short_description' => 'Master the fundamentals of computer programming',
        'rating_avg' => 5,
        'rating_count' => 89,
        'is_on_sale' => true,
        'is_new_arrival' => true,
        'stock_quantity' => 120,
        'json_attributes' => json_encode([
            'author' => 'John Smith',
            'publisher' => 'Tech Books Inc',
            'isbn' => '978-3-16-148410-0',
            'pages' => 450,
            'language' => 'English'
        ])
    ],
    [
        'id' => 3,
        'name' => 'Classic Cotton T-Shirt',
        'slug' => 'classic-cotton-tshirt',
        'price' => 24.99,
        'compare_price' => 39.99,
        'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400',
        'hover_image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=400',
        'category_name' => 'Fashion',
        'category_slug' => 'fashion',
        'short_description' => '100% organic cotton, comfortable fit',
        'rating_avg' => 4,
        'rating_count' => 256,
        'is_on_sale' => true,
        'is_new_arrival' => false,
        'stock_quantity' => 75,
        'json_attributes' => json_encode([
            'material' => '100% Cotton',
            'fit' => 'Regular',
            'pattern' => 'Solid',
            'care' => 'Machine Wash'
        ]),
        'variations' => [
            [
                'attributes' => json_encode(['color' => 'Red', 'size' => 'M']),
                'color_hex' => '#FF0000',
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400'
            ],
            [
                'attributes' => json_encode(['color' => 'Blue', 'size' => 'L']),
                'color_hex' => '#0000FF',
                'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=400'
            ],
            [
                'attributes' => json_encode(['color' => 'Black', 'size' => 'XL']),
                'color_hex' => '#000000',
                'image' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=400'
            ]
        ]
    ]
];
?>

<!-- Demo Container -->
<div class="container-fluid px-0">
    
    <!-- Header Demos -->
    <div class="demo-section">
        <div class="container-custom">
            <div class="demo-heading">
                <h1>Header Variations Demo</h1>
                <p class="text-muted">5 Different Header Layouts - Dynamically Switchable</p>
            </div>
            
            <div class="demo-note">
                <strong>📌 Current Active Header:</strong> Header V2 (Standard E-commerce)<br>
                <small>Change by updating <code>header_layout_id</code> in database</small>
            </div>
        </div>
        
        <?php 
        // Load active header
        $headerLayout = 2; // You can change this to 1-5
        $product = $sampleProducts[0]; // For card demo
        include dirname(__DIR__) . "/includes/headers/header-v{$headerLayout}.php";
        ?>
    </div>
    
    <!-- Product Cards Demo -->
    <div class="demo-section bg-light">
        <div class="container-custom">
            <div class="demo-heading">
                <h2>Product Card Variations</h2>
                <p class="text-muted">5 Different Card Styles for Different Product Types</p>
            </div>
            
            <!-- Card V1 - WoodMart Standard -->
            <div class="mb-5">
                <h3 class="mb-3"><span class="badge bg-primary">V1</span> WoodMart Standard</h3>
                <p class="text-muted mb-4">Universal card with quick view on hover</p>
                <div class="row">
                    <div class="col-md-3">
                        <?php $product = $sampleProducts[0]; include dirname(__DIR__) . '/includes/cards/card-v1.php'; ?>
                    </div>
                    <div class="col-md-3">
                        <?php $product = $sampleProducts[1]; include dirname(__DIR__) . '/includes/cards/card-v1.php'; ?>
                    </div>
                    <div class="col-md-3">
                        <?php $product = $sampleProducts[2]; include dirname(__DIR__) . '/includes/cards/card-v1.php'; ?>
                    </div>
                </div>
            </div>
            
            <hr class="my-5">
            
            <!-- Card V2 - Minimal -->
            <div class="mb-5">
                <h3 class="mb-3"><span class="badge bg-secondary">V2</span> Minimal Clean Design</h3>
                <p class="text-muted mb-4">Borderless, centered content, perfect for fashion</p>
                <div class="row">
                    <div class="col-md-3">
                        <?php $product = $sampleProducts[2]; include dirname(__DIR__) . '/includes/cards/card-v2.php'; ?>
                    </div>
                    <div class="col-md-3">
                        <?php $product = $sampleProducts[0]; include dirname(__DIR__) . '/includes/cards/card-v2.php'; ?>
                    </div>
                </div>
            </div>
            
            <hr class="my-5">
            
            <!-- Card V3 - Tech Specs -->
            <div class="mb-5">
                <h3 class="mb-3"><span class="badge bg-info">V3</span> Tech Specs (List View)</h3>
                <p class="text-muted mb-4">Horizontal layout with specifications, ideal for electronics</p>
                <div class="row">
                    <div class="col-12">
                        <?php $product = $sampleProducts[0]; include dirname(__DIR__) . '/includes/cards/card-v3.php'; ?>
                    </div>
                </div>
            </div>
            
            <hr class="my-5">
            
            <!-- Card V4 - Book Style -->
            <div class="mb-5">
                <h3 class="mb-3"><span class="badge bg-warning">V4</span> Book Store Style</h3>
                <p class="text-muted mb-4">3D book cover effect with shadow</p>
                <div class="row">
                    <div class="col-md-3">
                        <?php $product = $sampleProducts[1]; include dirname(__DIR__) . '/includes/cards/card-v4.php'; ?>
                    </div>
                </div>
            </div>
            
            <hr class="my-5">
            
            <!-- Card V5 - Fashion Swatch -->
            <div class="mb-5">
                <h3 class="mb-3"><span class="badge bg-danger">V5</span> Fashion Swatch Style</h3>
                <p class="text-muted mb-4">Color swatches with image switching, sizes, material tags</p>
                <div class="row">
                    <div class="col-md-4">
                        <?php $product = $sampleProducts[2]; include dirname(__DIR__) . '/includes/cards/card-v5.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Theme Settings Info -->
    <div class="demo-section">
        <div class="container-custom">
            <div class="demo-heading">
                <h2>Dynamic Theme Settings</h2>
                <p class="text-muted">All styling controlled from database</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Current Theme Settings</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <?php
                                $settings = ThemeHelper::loadSettings();
                                $displaySettings = [
                                    'primary_color' => 'Primary Color',
                                    'secondary_color' => 'Secondary Color',
                                    'font_family' => 'Font Family',
                                    'header_layout_id' => 'Header Layout',
                                    'product_card_style' => 'Card Style',
                                    'grid_columns' => 'Grid Columns',
                                    'lazy_load_enabled' => 'Lazy Loading'
                                ];
                                
                                foreach ($displaySettings as $key => $label):
                                    if (isset($settings[$key])):
                                ?>
                                    <tr>
                                        <td><strong><?php echo $label; ?>:</strong></td>
                                        <td>
                                            <?php 
                                            if ($key === 'primary_color' || $key === 'secondary_color') {
                                                echo '<span style="display:inline-block;width:20px;height:20px;background:' . $settings[$key] . ';border:1px solid #000;"></span> ';
                                            }
                                            echo is_bool($settings[$key]) ? ($settings[$key] ? 'Yes' : 'No') : $settings[$key]; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">How to Change Settings</h5>
                        </div>
                        <div class="card-body">
                            <p>Update settings via SQL:</p>
                            <pre class="bg-dark text-white p-3 rounded"><code>-- Change primary color
UPDATE theme_settings 
SET setting_value = '#FF5733' 
WHERE setting_key = 'primary_color';

-- Change header layout (1-5)
UPDATE theme_settings 
SET setting_value = '3' 
WHERE setting_key = 'header_layout_id';

-- Change card style (1-5)
UPDATE theme_settings 
SET setting_value = '4' 
WHERE setting_key = 'product_card_style';

-- Refresh page to see changes!</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="site-footer py-5">
        <div class="container-custom text-center text-white">
            <h4>WoodMart Clone</h4>
            <p>Premium E-commerce Framework with Dynamic Theme Engine</p>
            <p class="mb-0">Built with Core PHP, Bootstrap 5, and MySQL</p>
        </div>
    </footer>
    
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>

<!-- Demo specific JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎨 WoodMart Clone Visual Demo Loaded');
    console.log('✅ 5 Header Variations Available');
    console.log('✅ 5 Product Card Styles Available');
    console.log('✅ Dynamic Theme System Active');
});
</script>

</body>
</html>
