<?php
/**
 * Simple Router for PHP Built-in Server
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove query string
$path = strtok($path, '?');

// Check if file exists
$file = __DIR__ . $path;

// Handle admin routes
if (strpos($path, '/admin/') === 0) {
    $adminFile = dirname(__DIR__) . $path;
    if (file_exists($adminFile)) {
        require $adminFile;
        exit;
    }
}

// Handle API routes
if (strpos($path, '/api/') === 0 && file_exists($file)) {
    require $file;
    exit;
}

// Handle user routes
if (strpos($path, '/user/') === 0 && file_exists($file)) {
    require $file;
    exit;
}

// Static files
if (file_exists($file) && !is_dir($file)) {
    return false; // Let PHP serve the file
}

// Default - show demo page
if ($path === '/' || $path === '') {
    header('Location: /demo.php');
    exit;
}

// 404
http_response_code(404);
echo "404 - Page Not Found: $path";
