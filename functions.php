<?php
/**
 * Helper Functions
 * Utility functions used across the entire website
 */

require_once __DIR__ . '/config.php';

/**
 * Get a single setting value by key
 */
function getSetting(string $key): ?string {
    global $pdo;
    static $cache = [];
    
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $result = $stmt->fetchColumn();
        $cache[$key] = $result !== false ? $result : null;
        return $cache[$key];
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Get all settings as key-value array
 */
function getAllSettings(): array {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get SEO settings for a specific page
 */
function getSeoForPage(string $pageKey): array {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM seo_settings WHERE page_key = :page_key LIMIT 1");
        $stmt->execute([':page_key' => $pageKey]);
        $result = $stmt->fetch();
        return $result ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Sanitize user input
 */
function sanitizeInput($data): string {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate an uploaded image file
 */
function validateImage(array $file): array {
    $errors = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed. Please try again.';
        return $errors;
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        $errors[] = 'Invalid file type. Allowed: JPG, PNG, WebP, GIF.';
    }
    
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds 5MB limit.';
    }
    
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'File is not a valid image.';
    }
    
    return $errors;
}

/**
 * Generate a URL-friendly slug from text
 */
function generateSlug(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

/**
 * Format a date string
 */
function formatDate(?string $date, string $format = 'd M Y'): string {
    if (empty($date)) {
        return '';
    }
    try {
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Check if admin is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin login - redirect to login page if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get count of enquiries by type or status
 */
function getEnquiryCount(string $type = 'new'): int {
    global $pdo;
    
    try {
        if (in_array($type, ['new', 'contacted', 'converted', 'closed'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enquiries WHERE status = :type");
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enquiries WHERE enquiry_type = :type");
        }
        $stmt->execute([':type' => $type]);
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Redirect to a URL
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Get all active items from a table
 */
function getActiveItems(string $table, string $orderBy = 'id ASC'): array {
    global $pdo;
    
    $allowedTables = ['membership_plans', 'trainers', 'classes', 'gallery', 'transformations', 'testimonials', 'blog_posts'];
    
    if (!in_array($table, $allowedTables)) {
        return [];
    }
    
    try {
        $stmt = $pdo->query("SELECT * FROM `{$table}` WHERE is_active = 1 ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Upload an image file to a specified directory
 */
function uploadImage(array $file, string $directory = 'uploads/'): ?string {
    $errors = validateImage($file);
    if (!empty($errors)) {
        return null;
    }
    
    $uploadDir = ROOT_PATH . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . strtolower($extension);
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $directory . $filename;
    }
    
    return null;
}

/**
 * Delete an uploaded file
 */
function deleteImage(string $filepath): bool {
    $fullPath = ROOT_PATH . $filepath;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Truncate text to a specified length
 */
function truncateText(string $text, int $length = 150, string $suffix = '...'): string {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get published blog posts with optional limit
 */
function getPublishedPosts(int $limit = 10, int $offset = 0): array {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Output HTML-escaped text
 */
function e(?string $text): string {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate star rating HTML
 */
function renderStars(int $rating): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="fas fa-star"></i>';
        } else {
            $html .= '<i class="far fa-star"></i>';
        }
    }
    return $html;
}
