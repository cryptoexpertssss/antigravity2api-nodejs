-- ============================================
-- Add Missing Theme Settings
-- Run this if you need additional settings
-- ============================================

-- General Settings
INSERT INTO `theme_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`) VALUES
('site_logo', '', 'text', 'general', 'Site logo path'),
('site_favicon', '', 'text', 'general', 'Site favicon path'),
('maintenance_mode', 'false', 'boolean', 'general', 'Enable maintenance mode'),
('accent_color', '#ffd93d', 'color', 'colors', 'Accent color for highlights')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Shop Settings
INSERT INTO `theme_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`) VALUES
('catalog_mode', 'false', 'boolean', 'shop', 'Hide prices and purchase buttons'),
('ajax_search_enabled', 'true', 'boolean', 'shop', 'Enable AJAX live search')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Success message
SELECT 'Additional theme settings added successfully! ✅' AS message;
