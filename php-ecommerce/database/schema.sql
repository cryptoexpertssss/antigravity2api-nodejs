-- ============================================
-- WoodMart Clone Database Schema
-- Core PHP E-commerce System
-- Optimized for cPanel MySQL
-- ============================================

-- Set character set
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- USERS & AUTHENTICATION
-- ============================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('customer', 'admin', 'vendor') DEFAULT 'customer',
  `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CATEGORIES
-- ============================================

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(500) DEFAULT NULL,
  `icon` VARCHAR(100) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` BOOLEAN DEFAULT TRUE,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_parent` (`parent_id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PRODUCTS (Multi-Niche Support with JSON)
-- ============================================

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `sku` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(500) NOT NULL,
  `slug` VARCHAR(500) NOT NULL UNIQUE,
  `short_description` TEXT DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `compare_price` DECIMAL(10,2) DEFAULT NULL,
  `cost_price` DECIMAL(10,2) DEFAULT NULL,
  `stock_quantity` INT DEFAULT 0,
  `low_stock_threshold` INT DEFAULT 5,
  `weight` DECIMAL(8,2) DEFAULT NULL,
  `dimensions` VARCHAR(100) DEFAULT NULL COMMENT 'LxWxH',
  `product_type` ENUM('books', 'electronics', 'fashion', 'general') DEFAULT 'general',
  `json_attributes` JSON DEFAULT NULL COMMENT 'Books: ISBN/Author, Fashion: Material/Fit, Electronics: Specs',
  `is_featured` BOOLEAN DEFAULT FALSE,
  `is_new_arrival` BOOLEAN DEFAULT FALSE,
  `is_on_sale` BOOLEAN DEFAULT FALSE,
  `status` ENUM('draft', 'active', 'out_of_stock', 'discontinued') DEFAULT 'draft',
  `views_count` INT UNSIGNED DEFAULT 0,
  `sales_count` INT UNSIGNED DEFAULT 0,
  `rating_avg` DECIMAL(3,2) DEFAULT 0.00,
  `rating_count` INT UNSIGNED DEFAULT 0,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  INDEX `idx_sku` (`sku`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_type` (`product_type`),
  INDEX `idx_status` (`status`),
  INDEX `idx_featured` (`is_featured`),
  INDEX `idx_price` (`price`),
  FULLTEXT INDEX `idx_search` (`name`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example JSON attributes structure:
-- Books: {"isbn": "978-3-16-148410-0", "author": "John Doe", "publisher": "ABC Books", "pages": 350, "language": "English"}
-- Fashion: {"material": "Cotton", "fit": "Slim", "pattern": "Solid", "occasion": "Casual", "care": "Machine Wash"}
-- Electronics: {"brand": "Samsung", "model": "XYZ-123", "warranty": "1 Year", "specifications": {"ram": "8GB", "storage": "256GB"}}

-- ============================================
-- PRODUCT VARIATIONS (Size, Color, etc.)
-- ============================================

DROP TABLE IF EXISTS `variations`;
CREATE TABLE `variations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `sku` VARCHAR(100) NOT NULL UNIQUE,
  `variation_type` VARCHAR(50) NOT NULL COMMENT 'size, color, size-color, etc.',
  `attributes` JSON NOT NULL COMMENT '{"size": "L", "color": "Red"}',
  `price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Override product price if set',
  `compare_price` DECIMAL(10,2) DEFAULT NULL,
  `stock_quantity` INT DEFAULT 0,
  `image` VARCHAR(500) DEFAULT NULL,
  `is_default` BOOLEAN DEFAULT FALSE,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_product` (`product_id`),
  INDEX `idx_sku` (`sku`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PRODUCT IMAGES
-- ============================================

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(500) NOT NULL,
  `thumbnail_path` VARCHAR(500) DEFAULT NULL,
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_primary` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_product` (`product_id`),
  INDEX `idx_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- THEME SETTINGS (Dynamic Theme Engine)
-- ============================================

DROP TABLE IF EXISTS `theme_settings`;
CREATE TABLE `theme_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `setting_type` ENUM('color', 'text', 'number', 'boolean', 'json', 'select') DEFAULT 'text',
  `category` VARCHAR(50) DEFAULT 'general' COMMENT 'general, colors, typography, layout, etc.',
  `description` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`setting_key`),
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default theme settings
INSERT INTO `theme_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`) VALUES
-- Colors
('primary_color', '#ff6b6b', 'color', 'colors', 'Primary brand color'),
('secondary_color', '#4ecdc4', 'color', 'colors', 'Secondary accent color'),
('text_color', '#333333', 'color', 'colors', 'Main text color'),
('background_color', '#ffffff', 'color', 'colors', 'Page background color'),
('header_bg_color', '#1a1a1a', 'color', 'colors', 'Header background color'),
('footer_bg_color', '#2c2c2c', 'color', 'colors', 'Footer background color'),

-- Typography
('font_family', 'Poppins, sans-serif', 'text', 'typography', 'Primary font family'),
('font_size_base', '16', 'number', 'typography', 'Base font size in pixels'),
('heading_font', 'Montserrat, sans-serif', 'text', 'typography', 'Heading font family'),

-- Layout Options
('header_layout_id', '1', 'select', 'layout', 'Header layout style (1-5)'),
('product_card_style', '1', 'select', 'layout', 'Product card design (1-5)'),
('sidebar_position', 'left', 'select', 'layout', 'Sidebar position: left/right'),
('container_width', '1200', 'number', 'layout', 'Max container width in pixels'),
('grid_columns', '4', 'select', 'layout', 'Product grid columns (2-6)'),

-- Features
('lazy_load_enabled', 'true', 'boolean', 'features', 'Enable lazy loading for images'),
('quick_view_enabled', 'true', 'boolean', 'features', 'Enable quick view modal'),
('wishlist_enabled', 'true', 'boolean', 'features', 'Enable wishlist feature'),
('compare_enabled', 'true', 'boolean', 'features', 'Enable product comparison'),
('ajax_cart_enabled', 'true', 'boolean', 'features', 'Enable AJAX add to cart'),

-- Homepage Sections
('show_hero_slider', 'true', 'boolean', 'homepage', 'Display hero slider'),
('show_categories_section', 'true', 'boolean', 'homepage', 'Display categories section'),
('show_featured_products', 'true', 'boolean', 'homepage', 'Display featured products'),
('show_new_arrivals', 'true', 'boolean', 'homepage', 'Display new arrivals'),
('show_bestsellers', 'true', 'boolean', 'homepage', 'Display bestsellers'),

-- SEO
('site_name', 'WoodMart Clone', 'text', 'seo', 'Website name'),
('site_tagline', 'Premium Multi-Niche Store', 'text', 'seo', 'Website tagline'),
('meta_description', 'Shop books, electronics, and fashion at the best prices', 'text', 'seo', 'Default meta description'),
('meta_keywords', 'ecommerce, shop, online store', 'text', 'seo', 'Default meta keywords');

-- ============================================
-- MEGA MENU
-- ============================================

DROP TABLE IF EXISTS `mega_menu`;
CREATE TABLE `mega_menu` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `url` VARCHAR(500) DEFAULT NULL,
  `icon` VARCHAR(100) DEFAULT NULL,
  `badge_text` VARCHAR(50) DEFAULT NULL COMMENT 'NEW, HOT, SALE',
  `badge_color` VARCHAR(20) DEFAULT NULL,
  `mega_menu_enabled` BOOLEAN DEFAULT FALSE,
  `mega_menu_columns` INT DEFAULT 4,
  `mega_menu_content` TEXT DEFAULT NULL COMMENT 'HTML content for mega menu',
  `target` ENUM('_self', '_blank') DEFAULT '_self',
  `sort_order` INT DEFAULT 0,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `mega_menu`(`id`) ON DELETE CASCADE,
  INDEX `idx_parent` (`parent_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CART & CHECKOUT
-- ============================================

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(255) DEFAULT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `variation_id` INT UNSIGNED DEFAULT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variation_id`) REFERENCES `variations`(`id`) ON DELETE CASCADE,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
  `subtotal` DECIMAL(10,2) NOT NULL,
  `tax` DECIMAL(10,2) DEFAULT 0,
  `shipping_cost` DECIMAL(10,2) DEFAULT 0,
  `discount` DECIMAL(10,2) DEFAULT 0,
  `total` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'USD',
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `shipping_address` JSON NOT NULL,
  `billing_address` JSON NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_order_number` (`order_number`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `variation_id` INT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(500) NOT NULL,
  `sku` VARCHAR(100) NOT NULL,
  `variation_details` JSON DEFAULT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
  INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- REVIEWS & RATINGS
-- ============================================

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `title` VARCHAR(255) DEFAULT NULL,
  `comment` TEXT NOT NULL,
  `is_verified_purchase` BOOLEAN DEFAULT FALSE,
  `is_approved` BOOLEAN DEFAULT FALSE,
  `helpful_count` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_product` (`product_id`),
  INDEX `idx_approved` (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- WISHLIST
-- ============================================

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`),
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COUPONS & DISCOUNTS
-- ============================================

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `discount_type` ENUM('percentage', 'fixed', 'free_shipping') NOT NULL,
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_purchase` DECIMAL(10,2) DEFAULT 0,
  `max_discount` DECIMAL(10,2) DEFAULT NULL,
  `usage_limit` INT DEFAULT NULL,
  `usage_per_user` INT DEFAULT 1,
  `used_count` INT DEFAULT 0,
  `start_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `end_date` TIMESTAMP DEFAULT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_code` (`code`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SITE SETTINGS & SEO
-- ============================================

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT DEFAULT NULL,
  `category` VARCHAR(50) DEFAULT 'general',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ENABLE FOREIGN KEY CHECKS
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SAMPLE DATA (OPTIONAL)
-- ============================================

-- Sample Categories
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
('Books', 'books', 'Explore our collection of books', 1, TRUE),
('Electronics', 'electronics', 'Latest electronics and gadgets', 2, TRUE),
('Fashion', 'fashion', 'Trendy fashion clothing and accessories', 3, TRUE),
('Fiction', 'fiction', 'Fiction books and novels', 1, TRUE),
('Non-Fiction', 'non-fiction', 'Educational and non-fiction books', 2, TRUE),
('Smartphones', 'smartphones', 'Latest smartphones', 1, TRUE),
('Laptops', 'laptops', 'High-performance laptops', 2, TRUE),
('Men', 'men-fashion', 'Men\'s clothing', 1, TRUE),
('Women', 'women-fashion', 'Women\'s clothing', 2, TRUE);

-- Update parent_id for subcategories
UPDATE `categories` SET `parent_id` = 1 WHERE `slug` IN ('fiction', 'non-fiction');
UPDATE `categories` SET `parent_id` = 2 WHERE `slug` IN ('smartphones', 'laptops');
UPDATE `categories` SET `parent_id` = 3 WHERE `slug` IN ('men-fashion', 'women-fashion');

-- Sample Admin User (password: admin123)
INSERT INTO `users` (`email`, `password`, `first_name`, `last_name`, `role`, `status`, `email_verified_at`) VALUES
('admin@woodmart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 'active', NOW());

-- Success message
SELECT 'Database schema created successfully! ✅' AS message;
