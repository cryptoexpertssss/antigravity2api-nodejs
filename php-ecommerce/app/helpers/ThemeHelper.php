<?php
/**
 * Theme Helper Class
 * Loads and manages theme settings from database
 */

class ThemeHelper {
    private static $settings = null;
    
    /**
     * Load all theme settings from database
     */
    public static function loadSettings() {
        if (self::$settings !== null) {
            return self::$settings;
        }
        
        try {
            $db = Database::getInstance();
            $results = $db->fetchAll("SELECT setting_key, setting_value, setting_type FROM theme_settings");
            
            self::$settings = [];
            foreach ($results as $row) {
                $value = $row['setting_value'];
                
                // Convert boolean strings
                if ($row['setting_type'] === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                // Convert numbers
                elseif ($row['setting_type'] === 'number') {
                    $value = is_numeric($value) ? (float)$value : $value;
                }
                // Parse JSON
                elseif ($row['setting_type'] === 'json') {
                    $value = json_decode($value, true);
                }
                
                self::$settings[$row['setting_key']] = $value;
            }
            
            return self::$settings;
            
        } catch (Exception $e) {
            error_log("ThemeHelper Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a specific theme setting
     */
    public static function get($key, $default = null) {
        if (self::$settings === null) {
            self::loadSettings();
        }
        
        return self::$settings[$key] ?? $default;
    }
    
    /**
     * Get header layout ID
     */
    public static function getHeaderLayout() {
        return self::get('header_layout_id', '1');
    }
    
    /**
     * Get product card style
     */
    public static function getCardStyle() {
        return self::get('product_card_style', '1');
    }
    
    /**
     * Check if feature is enabled
     */
    public static function isEnabled($feature) {
        return self::get($feature, false) === true;
    }
}
