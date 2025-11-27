<?php
/**
 * Dynamic CSS Generator
 * Generates CSS from database theme_settings
 * Usage: <link rel="stylesheet" href="assets/dynamic-style.php">
 */

header('Content-Type: text/css');
header('Cache-Control: max-age=3600'); // Cache for 1 hour

require_once dirname(__DIR__, 2) . '/app/helpers/Database.php';
require_once dirname(__DIR__, 2) . '/app/helpers/ThemeHelper.php';

// Load theme settings
$theme = ThemeHelper::loadSettings();

// Extract colors
$primaryColor = $theme['primary_color'] ?? '#ff6b6b';
$secondaryColor = $theme['secondary_color'] ?? '#4ecdc4';
$textColor = $theme['text_color'] ?? '#333333';
$bgColor = $theme['background_color'] ?? '#ffffff';
$headerBg = $theme['header_bg_color'] ?? '#1a1a1a';
$footerBg = $theme['footer_bg_color'] ?? '#2c2c2c';

// Typography
$fontFamily = $theme['font_family'] ?? 'Poppins, sans-serif';
$fontSize = $theme['font_size_base'] ?? '16';
$headingFont = $theme['heading_font'] ?? 'Montserrat, sans-serif';

// Layout
$containerWidth = $theme['container_width'] ?? '1200';
$gridColumns = $theme['grid_columns'] ?? '4';

// Calculate hover colors (darken primary by 10%)
function adjustBrightness($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
               . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
               . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

$primaryHover = adjustBrightness($primaryColor, -10);
$secondaryHover = adjustBrightness($secondaryColor, -10);

?>

:root {
    /* Colors */
    --primary-color: <?php echo $primaryColor; ?>;
    --primary-hover: <?php echo $primaryHover; ?>;
    --secondary-color: <?php echo $secondaryColor; ?>;
    --secondary-hover: <?php echo $secondaryHover; ?>;
    --text-color: <?php echo $textColor; ?>;
    --bg-color: <?php echo $bgColor; ?>;
    --header-bg: <?php echo $headerBg; ?>;
    --footer-bg: <?php echo $footerBg; ?>;
    
    /* Typography */
    --font-family: <?php echo $fontFamily; ?>;
    --font-size: <?php echo $fontSize; ?>px;
    --heading-font: <?php echo $headingFont; ?>;
    
    /* Layout */
    --container-width: <?php echo $containerWidth; ?>px;
    --grid-columns: <?php echo $gridColumns; ?>;
    
    /* Spacing */
    --spacing-xs: 0.5rem;
    --spacing-sm: 1rem;
    --spacing-md: 1.5rem;
    --spacing-lg: 2rem;
    --spacing-xl: 3rem;
    
    /* Border Radius */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    
    /* Shadows */
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.15);
    --shadow-xl: 0 20px 40px rgba(0,0,0,0.2);
    
    /* Transitions */
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;
}

/* Global Styles */
body {
    font-family: var(--font-family);
    font-size: var(--font-size);
    color: var(--text-color);
    background-color: var(--bg-color);
    line-height: 1.6;
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--heading-font);
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: var(--spacing-sm);
}

/* Container */
.container-custom {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 var(--spacing-md);
}

/* Primary Button */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: #ffffff;
    transition: all var(--transition-fast);
    border-radius: var(--radius-md);
    font-weight: 500;
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Secondary Button */
.btn-secondary {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
    color: #ffffff;
    transition: all var(--transition-fast);
    border-radius: var(--radius-md);
    font-weight: 500;
}

.btn-secondary:hover {
    background-color: var(--secondary-hover);
    border-color: var(--secondary-hover);
    color: #ffffff;
}

/* Links */
a {
    color: var(--primary-color);
    text-decoration: none;
    transition: color var(--transition-fast);
}

a:hover {
    color: var(--primary-hover);
}

/* Header */
.site-header {
    background-color: var(--header-bg);
    color: #ffffff;
}

/* Footer */
.site-footer {
    background-color: var(--footer-bg);
    color: #ffffff;
}

/* Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns), 1fr);
    gap: var(--spacing-md);
}

@media (max-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
}

/* Badge Styles */
.badge-primary {
    background-color: var(--primary-color);
}

.badge-secondary {
    background-color: var(--secondary-color);
}

/* Card Styles */
.card {
    border-radius: var(--radius-lg);
    border: 1px solid #e0e0e0;
    transition: all var(--transition-normal);
}

.card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
}

/* Input Styles */
.form-control {
    border-radius: var(--radius-md);
    border: 1px solid #ddd;
    padding: 0.75rem 1rem;
    transition: all var(--transition-fast);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
}

/* Loading Animation */
.loading-spinner {
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-left-color: var(--primary-color);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Utility Classes */
.text-primary { color: var(--primary-color); }
.bg-primary { background-color: var(--primary-color); }
.text-secondary { color: var(--secondary-color); }
.bg-secondary { background-color: var(--secondary-color); }
