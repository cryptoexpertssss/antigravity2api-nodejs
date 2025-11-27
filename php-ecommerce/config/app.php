<?php
/**
 * Application Configuration
 */

return [
    'name' => 'WoodMart Clone',
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'debug' => true,
    
    // Paths
    'base_path' => dirname(__DIR__),
    'public_path' => dirname(__DIR__) . '/public',
    'storage_path' => dirname(__DIR__) . '/storage',
    'theme_path' => dirname(__DIR__) . '/themes',
    
    // Security
    'session_lifetime' => 120, // minutes
    'session_name' => 'WOODMART_SESSION',
    
    // Upload settings
    'max_upload_size' => 5242880, // 5MB in bytes
    'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
];
