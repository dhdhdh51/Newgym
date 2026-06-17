<?php
/**
 * Dynamic Robots.txt
 * Generates robots.txt for search engines
 */
require_once 'config.php';

// Set plain text content type
header('Content-Type: text/plain; charset=utf-8');

$siteUrl = rtrim(SITE_URL, '/');
?>
User-agent: *
Allow: /

Disallow: /admin/
Disallow: /uploads/
Disallow: /config.php
Disallow: /functions.php

Sitemap: <?php echo $siteUrl; ?>/sitemap.php
