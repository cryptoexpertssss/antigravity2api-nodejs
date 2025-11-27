<?php
/**
 * SEO & Social Sharing Meta Tags
 * Dynamic OpenGraph tags for Facebook/WhatsApp/Twitter
 */

// Default values
$pageTitle = $pageTitle ?? 'WoodMart Clone - Premium E-commerce Store';
$pageDescription = $pageDescription ?? 'Shop books, electronics, and fashion at the best prices';
$pageImage = $pageImage ?? '/assets/images/og-default.jpg';
$pageUrl = $pageUrl ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$pageType = $pageType ?? 'website';
$siteName = 'WoodMart Clone';

// Product specific
$productPrice = $productPrice ?? null;
$productCurrency = $productCurrency ?? 'USD';
$productAvailability = $productAvailability ?? 'in stock';
?>

<!-- Primary Meta Tags -->
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<meta name="title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta name="keywords" content="ecommerce, online shopping, books, electronics, fashion, woodmart">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="author" content="WoodMart">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?php echo $pageType; ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($pageUrl); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($pageImage); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
<meta property="og:locale" content="en_US">

<?php if ($productPrice): ?>
<!-- Product Specific -->
<meta property="product:price:amount" content="<?php echo $productPrice; ?>">
<meta property="product:price:currency" content="<?php echo $productCurrency; ?>">
<meta property="product:availability" content="<?php echo $productAvailability; ?>">
<?php endif; ?>

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo htmlspecialchars($pageUrl); ?>">
<meta property="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta property="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta property="twitter:image" content="<?php echo htmlspecialchars($pageImage); ?>">

<!-- WhatsApp / Telegram -->
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo htmlspecialchars($pageTitle); ?>">

<!-- Canonical URL -->
<link rel="canonical" href="<?php echo htmlspecialchars($pageUrl); ?>">

<!-- Favicon -->
<?php
$favicon = ThemeHelper::get('site_favicon', '/favicon.ico');
?>
<link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">
<link rel="apple-touch-icon" href="<?php echo $favicon; ?>">

<!-- JSON-LD Structured Data -->
<?php if ($pageType === 'product' && isset($product)): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?php echo htmlspecialchars($product['name'] ?? $pageTitle); ?>",
  "image": "<?php echo htmlspecialchars($pageImage); ?>",
  "description": "<?php echo htmlspecialchars($pageDescription); ?>",
  "sku": "<?php echo htmlspecialchars($product['sku'] ?? ''); ?>",
  "brand": {
    "@type": "Brand",
    "name": "<?php echo htmlspecialchars($siteName); ?>"
  },
  <?php if (isset($product['rating_avg']) && $product['rating_avg'] > 0): ?>
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?php echo $product['rating_avg']; ?>",
    "reviewCount": "<?php echo $product['rating_count'] ?? 0; ?>"
  },
  <?php endif; ?>
  "offers": {
    "@type": "Offer",
    "url": "<?php echo htmlspecialchars($pageUrl); ?>",
    "priceCurrency": "<?php echo $productCurrency; ?>",
    "price": "<?php echo $productPrice ?? $product['price'] ?? 0; ?>",
    "availability": "https://schema.org/<?php echo ($product['stock_quantity'] ?? 0) > 0 ? 'InStock' : 'OutOfStock'; ?>"
  }
}
</script>
<?php endif; ?>
