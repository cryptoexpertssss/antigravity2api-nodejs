<?php
/**
 * Theme Controller
 * Handles theme settings fetching and updating
 */

class ThemeController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all theme settings
     */
    public function getAllSettings() {
        $sql = "SELECT * FROM theme_settings ORDER BY category, setting_key";
        $results = $this->db->fetchAll($sql);
        
        $settings = [];
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
            
            $settings[$row['setting_key']] = [
                'value' => $value,
                'type' => $row['setting_type'],
                'category' => $row['category'],
                'description' => $row['description']
            ];
        }
        
        return $settings;
    }
    
    /**
     * Get single setting value
     */
    public function getSetting($key, $default = null) {
        $sql = "SELECT setting_value, setting_type FROM theme_settings WHERE setting_key = :key";
        $result = $this->db->fetch($sql, ['key' => $key]);
        
        if (!$result) {
            return $default;
        }
        
        $value = $result['setting_value'];
        
        // Convert based on type
        if ($result['setting_type'] === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } elseif ($result['setting_type'] === 'number') {
            return is_numeric($value) ? (float)$value : $value;
        } elseif ($result['setting_type'] === 'json') {
            return json_decode($value, true);
        }
        
        return $value;
    }
    
    /**
     * Update or insert a setting
     */
    public function updateSetting($key, $value, $type = 'text', $category = 'general', $description = null) {
        // Convert boolean to string
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
            $type = 'boolean';
        }
        // Convert array/object to JSON
        elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $type = 'json';
        }
        
        // Check if setting exists
        $existing = $this->db->fetch(
            "SELECT id FROM theme_settings WHERE setting_key = :key",
            ['key' => $key]
        );
        
        if ($existing) {
            // Update existing
            $sql = "UPDATE theme_settings 
                    SET setting_value = :value, 
                        setting_type = :type,
                        category = :category,
                        description = :description,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE setting_key = :key";
            
            return $this->db->query($sql, [
                'value' => $value,
                'type' => $type,
                'category' => $category,
                'description' => $description,
                'key' => $key
            ]);
        } else {
            // Insert new
            return $this->db->insert('theme_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => $type,
                'category' => $category,
                'description' => $description
            ]);
        }
    }
    
    /**
     * Update multiple settings at once
     */
    public function updateMultipleSettings($settings) {
        $this->db->beginTransaction();
        
        try {
            foreach ($settings as $key => $data) {
                $value = $data['value'] ?? $data;
                $type = $data['type'] ?? 'text';
                $category = $data['category'] ?? 'general';
                $description = $data['description'] ?? null;
                
                $this->updateSetting($key, $value, $type, $category, $description);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Failed to update settings: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Handle file upload (logo, favicon, etc.)
     */
    public function handleFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg']) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'Invalid file upload'];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload failed with error code: ' . $file['error']];
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5242880) {
            return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
        }
        
        // Get file extension
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Validate file type
        if (!in_array($extension, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
        }
        
        // Generate unique filename
        $newFilename = uniqid('upload_', true) . '.' . $extension;
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
        $uploadPath = $uploadDir . $newFilename;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'filename' => $newFilename,
                'path' => '/uploads/' . $newFilename,
                'full_path' => $uploadPath
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
    
    /**
     * Delete uploaded file
     */
    public function deleteFile($filename) {
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
        $filePath = $uploadDir . basename($filename);
        
        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
    
    /**
     * Get settings by category
     */
    public function getSettingsByCategory($category) {
        $sql = "SELECT * FROM theme_settings WHERE category = :category ORDER BY setting_key";
        return $this->db->fetchAll($sql, ['category' => $category]);
    }
    
    /**
     * Reset settings to default
     */
    public function resetToDefaults() {
        // This would reset all settings to their default values
        // Implementation depends on your requirements
        return true;
    }
    
    /**
     * Export settings as JSON
     */
    public function exportSettings() {
        $settings = $this->getAllSettings();
        return json_encode($settings, JSON_PRETTY_PRINT);
    }
    
    /**
     * Import settings from JSON
     */
    public function importSettings($json) {
        try {
            $settings = json_decode($json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'message' => 'Invalid JSON format'];
            }
            
            $this->updateMultipleSettings($settings);
            
            return ['success' => true, 'message' => 'Settings imported successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
