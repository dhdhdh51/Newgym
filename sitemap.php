<?php
/**
 * Dynamic XML Sitemap
 * Generates sitemap.xml for search engines
 */
require_once 'config.php';
require_once 'functions.php';

// Set XML content type
header('Content-Type: application/xml; charset=utf-8');

// Get base URL
$siteUrl = rtrim(SITE_URL, '/');
$today = date('Y-m-d');

// Get published blog posts
$blogPosts = getPublishedPosts(1000, 0);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Main Pages -->
    <url>
        <loc><?php echo e($siteUrl); ?>/index.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/about.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/services.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/plans.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/trainers.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/classes.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/gallery.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/blog.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo e($siteUrl); ?>/contact.php</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Blog Posts -->
<?php foreach ($blogPosts as $post): ?>
    <url>
        <loc><?php echo e($siteUrl); ?>/post.php?slug=<?php echo e($post['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($post['updated_at'] ?? $post['published_at'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
<?php endforeach; ?>
</urlset>
