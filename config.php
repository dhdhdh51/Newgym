<?php
/**
 * Configuration File
 * Database connection, session management, and global settings
 */

// =====================================================
// Database Configuration - UPDATE THESE VALUES
// =====================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'gym_website');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// Error Reporting
// =====================================================
// Set to false in production
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// =====================================================
// Session Configuration
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    session_start();
}

// =====================================================
// Database Connection (PDO)
// =====================================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        die("Database Connection Failed: " . $e->getMessage());
    } else {
        die("A database error occurred. Please try again later.");
    }
}

// =====================================================
// Base URL Detection
// =====================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . '://' . $host . $scriptDir);
define('SITE_URL', $protocol . '://' . $host . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

// =====================================================
// Directory Paths
// =====================================================
define('ROOT_PATH', __DIR__ . '/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// =====================================================
// CSRF Token Generation
// =====================================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// =====================================================
// Security Headers
// =====================================================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
