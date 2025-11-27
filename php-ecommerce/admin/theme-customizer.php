<?php
/**
 * Admin Theme Customizer Panel
 * Comprehensive theme options management
 */

// Load dependencies
require_once dirname(__DIR__) . '/app/helpers/Database.php';
require_once dirname(__DIR__) . '/app/helpers/AuthHelper.php';
require_once dirname(__DIR__) . '/app/controllers/ThemeController.php';

// Require admin authentication
AuthHelper::requireAdmin();

// Initialize controller
$themeController = new ThemeController();

// Handle form submission
$message = null;
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !AuthHelper::verifyCSRFToken($_POST['csrf_token'])) {
        $message = 'Invalid security token. Please try again.';
        $messageType = 'danger';
    } else {
        // Prepare settings array
        $settings = [];
        
        // General Settings
        if (isset($_POST['site_name'])) {
            $settings['site_name'] = [
                'value' => $_POST['site_name'],
                'type' => 'text',
                'category' => 'general',
                'description' => 'Website name'
            ];
        }
        
        if (isset($_POST['site_tagline'])) {
            $settings['site_tagline'] = [
                'value' => $_POST['site_tagline'],
                'type' => 'text',
                'category' => 'general',
                'description' => 'Website tagline'
            ];
        }
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $themeController->handleFileUpload($_FILES['logo'], ['jpg', 'jpeg', 'png', 'svg']);
            if ($uploadResult['success']) {
                $settings['site_logo'] = [
                    'value' => $uploadResult['path'],
                    'type' => 'text',
                    'category' => 'general',
                    'description' => 'Site logo path'
                ];
            } else {
                $message = 'Logo upload failed: ' . $uploadResult['message'];
                $messageType = 'danger';
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $themeController->handleFileUpload($_FILES['favicon'], ['ico', 'png']);
            if ($uploadResult['success']) {
                $settings['site_favicon'] = [
                    'value' => $uploadResult['path'],
                    'type' => 'text',
                    'category' => 'general',
                    'description' => 'Site favicon path'
                ];
            } else {
                $message = 'Favicon upload failed: ' . $uploadResult['message'];
                $messageType = 'danger';
            }
        }
        
        // Maintenance mode
        $settings['maintenance_mode'] = [
            'value' => isset($_POST['maintenance_mode']) ? 'true' : 'false',
            'type' => 'boolean',
            'category' => 'general',
            'description' => 'Enable maintenance mode'
        ];
        
        // Style Settings - Colors
        if (isset($_POST['primary_color'])) {
            $settings['primary_color'] = [
                'value' => $_POST['primary_color'],
                'type' => 'color',
                'category' => 'colors',
                'description' => 'Primary brand color'
            ];
        }
        
        if (isset($_POST['secondary_color'])) {
            $settings['secondary_color'] = [
                'value' => $_POST['secondary_color'],
                'type' => 'color',
                'category' => 'colors',
                'description' => 'Secondary accent color'
            ];
        }
        
        if (isset($_POST['accent_color'])) {
            $settings['accent_color'] = [
                'value' => $_POST['accent_color'],
                'type' => 'color',
                'category' => 'colors',
                'description' => 'Accent color for highlights'
            ];
        }
        
        // Layout Settings
        if (isset($_POST['header_layout_id'])) {
            $settings['header_layout_id'] = [
                'value' => $_POST['header_layout_id'],
                'type' => 'select',
                'category' => 'layout',
                'description' => 'Header layout style (1-5)'
            ];
        }
        
        if (isset($_POST['product_card_style'])) {
            $settings['product_card_style'] = [
                'value' => $_POST['product_card_style'],
                'type' => 'select',
                'category' => 'layout',
                'description' => 'Product card design (1-5)'
            ];
        }
        
        // Shop Settings
        $settings['quick_view_enabled'] = [
            'value' => isset($_POST['quick_view_enabled']) ? 'true' : 'false',
            'type' => 'boolean',
            'category' => 'shop',
            'description' => 'Enable quick view modal'
        ];
        
        $settings['catalog_mode'] = [
            'value' => isset($_POST['catalog_mode']) ? 'true' : 'false',
            'type' => 'boolean',
            'category' => 'shop',
            'description' => 'Hide prices and purchase buttons'
        ];
        
        $settings['ajax_search_enabled'] = [
            'value' => isset($_POST['ajax_search_enabled']) ? 'true' : 'false',
            'type' => 'boolean',
            'category' => 'shop',
            'description' => 'Enable AJAX live search'
        ];
        
        // Update settings
        if (empty($message)) {
            if ($themeController->updateMultipleSettings($settings)) {
                $message = 'Settings saved successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to save settings. Please try again.';
                $messageType = 'danger';
            }
        }
    }
}

// Fetch current settings
$currentSettings = $themeController->getAllSettings();

// Helper function to get setting value
function getSettingValue($settings, $key, $default = '') {
    return isset($settings[$key]) ? $settings[$key]['value'] : $default;
}

// Generate CSRF token
$csrfToken = AuthHelper::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Customizer - WoodMart Admin</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --admin-primary: #2c3e50;
            --admin-secondary: #3498db;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--admin-primary);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-sidebar .logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .logo h4 {
            margin: 0;
            color: white;
        }
        
        .admin-sidebar .nav-menu {
            padding: 1rem 0;
        }
        
        .admin-sidebar .nav-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .admin-sidebar .nav-item:hover,
        .admin-sidebar .nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .admin-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 2rem;
        }
        
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .settings-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .settings-card h5 {
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--admin-secondary);
            color: var(--admin-primary);
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .color-input-wrapper {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .color-input-wrapper input[type="color"] {
            width: 60px;
            height: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .color-input-wrapper input[type="text"] {
            flex: 1;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 100px;
            margin-top: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
        }
        
        .btn-save {
            background: var(--admin-secondary);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-save:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo">
            <h4><i class="bi bi-gear-fill"></i> WoodMart Admin</h4>
        </div>
        <nav class="nav-menu">
            <a href="/admin/dashboard.php" class="nav-item">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin/theme-customizer.php" class="nav-item active">
                <i class="bi bi-palette"></i>
                <span>Theme Customizer</span>
            </a>
            <a href="/admin/products.php" class="nav-item">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>
            <a href="/admin/categories.php" class="nav-item">
                <i class="bi bi-tags"></i>
                <span>Categories</span>
            </a>
            <a href="/admin/orders.php" class="nav-item">
                <i class="bi bi-cart-check"></i>
                <span>Orders</span>
            </a>
            <a href="/admin/users.php" class="nav-item">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>
            <a href="/admin/settings.php" class="nav-item">
                <i class="bi bi-sliders"></i>
                <span>Settings</span>
            </a>
            <a href="/admin/logout.php" class="nav-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Theme Customizer</h2>
                    <p class="text-muted mb-0">Customize your store's appearance without coding</p>
                </div>
                <div>
                    <a href="/" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i> Preview Site
                    </a>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <!-- General Settings -->
            <div class="settings-card">
                <h5><i class="bi bi-gear"></i> General Settings</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?php echo htmlspecialchars(getSettingValue($currentSettings, 'site_name', 'WoodMart Clone')); ?>">
                        <div class="form-text">Displayed in header and browser title</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="site_tagline" class="form-label">Site Tagline</label>
                        <input type="text" class="form-control" id="site_tagline" name="site_tagline" 
                               value="<?php echo htmlspecialchars(getSettingValue($currentSettings, 'site_tagline', 'Premium Multi-Niche Store')); ?>">
                        <div class="form-text">Short description of your store</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="logo" class="form-label">Upload Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.svg">
                        <div class="form-text">Recommended size: 200x60px (JPG, PNG, SVG)</div>
                        <?php 
                        $currentLogo = getSettingValue($currentSettings, 'site_logo');
                        if ($currentLogo): 
                        ?>
                            <img src="<?php echo htmlspecialchars($currentLogo); ?>" alt="Current Logo" class="preview-image">
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="favicon" class="form-label">Upload Favicon</label>
                        <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,.png">
                        <div class="form-text">16x16 or 32x32 pixels (ICO, PNG)</div>
                        <?php 
                        $currentFavicon = getSettingValue($currentSettings, 'site_favicon');
                        if ($currentFavicon): 
                        ?>
                            <img src="<?php echo htmlspecialchars($currentFavicon); ?>" alt="Current Favicon" class="preview-image" style="max-width: 50px; max-height: 50px;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                           <?php echo getSettingValue($currentSettings, 'maintenance_mode', false) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="maintenance_mode">
                        <strong>Enable Maintenance Mode</strong>
                        <div class="form-text">Show maintenance page to visitors (admins can still access)</div>
                    </label>
                </div>
            </div>

            <!-- Style Settings -->
            <div class="settings-card">
                <h5><i class="bi bi-palette-fill"></i> Style Settings</h5>
                
                <h6 class="mb-3">Colors</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="primary_color" class="form-label">Primary Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="primary_color_picker" 
                                   value="<?php echo getSettingValue($currentSettings, 'primary_color', '#ff6b6b'); ?>"
                                   onchange="document.getElementById('primary_color').value = this.value">
                            <input type="text" class="form-control" id="primary_color" name="primary_color" 
                                   value="<?php echo getSettingValue($currentSettings, 'primary_color', '#ff6b6b'); ?>"
                                   onchange="document.getElementById('primary_color_picker').value = this.value">
                        </div>
                        <div class="form-text">Main brand color</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="secondary_color" class="form-label">Secondary Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="secondary_color_picker" 
                                   value="<?php echo getSettingValue($currentSettings, 'secondary_color', '#4ecdc4'); ?>"
                                   onchange="document.getElementById('secondary_color').value = this.value">
                            <input type="text" class="form-control" id="secondary_color" name="secondary_color" 
                                   value="<?php echo getSettingValue($currentSettings, 'secondary_color', '#4ecdc4'); ?>"
                                   onchange="document.getElementById('secondary_color_picker').value = this.value">
                        </div>
                        <div class="form-text">Accent color</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="accent_color" class="form-label">Accent Color</label>
                        <div class="color-input-wrapper">
                            <input type="color" id="accent_color_picker" 
                                   value="<?php echo getSettingValue($currentSettings, 'accent_color', '#ffd93d'); ?>"
                                   onchange="document.getElementById('accent_color').value = this.value">
                            <input type="text" class="form-control" id="accent_color" name="accent_color" 
                                   value="<?php echo getSettingValue($currentSettings, 'accent_color', '#ffd93d'); ?>"
                                   onchange="document.getElementById('accent_color_picker').value = this.value">
                        </div>
                        <div class="form-text">Highlight color</div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h6 class="mb-3">Layout Options</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="header_layout_id" class="form-label">Header Layout Style</label>
                        <select class="form-select" id="header_layout_id" name="header_layout_id">
                            <?php
                            $currentHeader = getSettingValue($currentSettings, 'header_layout_id', '1');
                            $headerOptions = [
                                '1' => 'V1 - Logo Center, Menu Split',
                                '2' => 'V2 - Standard E-commerce',
                                '3' => 'V3 - Vertical Sidebar Menu',
                                '4' => 'V4 - Minimal Transparent',
                                '5' => 'V5 - Mobile-First Bottom Nav'
                            ];
                            foreach ($headerOptions as $value => $label):
                            ?>
                                <option value="<?php echo $value; ?>" <?php echo $currentHeader == $value ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Choose header design (1-5)</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="product_card_style" class="form-label">Product Card Style</label>
                        <select class="form-select" id="product_card_style" name="product_card_style">
                            <?php
                            $currentCard = getSettingValue($currentSettings, 'product_card_style', '1');
                            $cardOptions = [
                                '1' => 'V1 - WoodMart Standard',
                                '2' => 'V2 - Minimal Clean',
                                '3' => 'V3 - Tech Specs (List)',
                                '4' => 'V4 - Book Store Style',
                                '5' => 'V5 - Fashion Swatch'
                            ];
                            foreach ($cardOptions as $value => $label):
                            ?>
                                <option value="<?php echo $value; ?>" <?php echo $currentCard == $value ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Choose product card design (1-5)</div>
                    </div>
                </div>
            </div>

            <!-- Shop Settings -->
            <div class="settings-card">
                <h5><i class="bi bi-shop"></i> Shop Settings</h5>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="quick_view_enabled" name="quick_view_enabled" 
                           <?php echo getSettingValue($currentSettings, 'quick_view_enabled', true) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="quick_view_enabled">
                        <strong>Enable Quick View</strong>
                        <div class="form-text">Allow customers to preview products in a modal popup</div>
                    </label>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="catalog_mode" name="catalog_mode" 
                           <?php echo getSettingValue($currentSettings, 'catalog_mode', false) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="catalog_mode">
                        <strong>Catalog Mode</strong>
                        <div class="form-text">Hide prices and "Add to Cart" buttons (display-only mode)</div>
                    </label>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="ajax_search_enabled" name="ajax_search_enabled" 
                           <?php echo getSettingValue($currentSettings, 'ajax_search_enabled', true) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ajax_search_enabled">
                        <strong>Enable AJAX Search</strong>
                        <div class="form-text">Show live search results as user types</div>
                    </label>
                </div>
            </div>

            <!-- Save Button -->
            <div class="text-end">
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-check-circle"></i> Save All Settings
                </button>
            </div>
        </form>
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Confirm before enabling maintenance mode
document.getElementById('maintenance_mode').addEventListener('change', function() {
    if (this.checked) {
        if (!confirm('Are you sure you want to enable maintenance mode? Visitors will see a maintenance page.')) {
            this.checked = false;
        }
    }
});

console.log('🎨 Theme Customizer loaded');
</script>

</body>
</html>
