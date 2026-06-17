<?php
/**
 * Single Blog Post Page
 * Display a full blog post by slug
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'blog';

$slug = $_GET['slug'] ?? '';
$post = null;

if (!empty($slug)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = :slug AND status = 'published' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $post = $stmt->fetch();
    } catch (PDOException $e) {
        $post = null;
    }
}

// If post not found, show 404 message
if (!$post) {
    $seo = getSeoForPage('blog');
    include 'header.php';
    ?>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Post Not Found</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> <span>/</span> <a href="blog.php">Blog</a> <span>/</span> <span>Not Found</span>
            </nav>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Post Not Found</h3>
                <p>The blog post you are looking for does not exist or has been removed.</p>
                <a href="blog.php" class="btn btn-primary">Back to Blog</a>
            </div>
        </div>
    </section>
    <?php
    include 'footer.php';
    exit;
}

// Override SEO with blog post meta
$seoData = [
    'meta_title' => $post['meta_title'] ?: $post['title'],
    'meta_description' => $post['meta_description'] ?: truncateText(strip_tags($post['content']), 160, ''),
    'meta_keywords' => $post['meta_keywords'] ?? '',
    'canonical_url' => $post['canonical_url'] ?? '',
    'schema_json' => $post['schema_json'] ?? '',
    'robots_meta' => 'index, follow'
];

// Get related posts (same category, exclude current)
$relatedPosts = [];
if (!empty($post['category'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE category = :category AND id != :id AND status = 'published' ORDER BY published_at DESC LIMIT 3");
        $stmt->execute([':category' => $post['category'], ':id' => $post['id']]);
        $relatedPosts = $stmt->fetchAll();
    } catch (PDOException $e) {
        $relatedPosts = [];
    }
}

$allSettings = getAllSettings();

// Custom header for blog post SEO
$metaTitle = $seoData['meta_title'];
$metaDescription = $seoData['meta_description'];
$metaKeywords = $seoData['meta_keywords'];
$canonicalUrl = $seoData['canonical_url'];
$schemaJson = $seoData['schema_json'];

include 'header.php';
?>

<!-- Page Header / Breadcrumbs -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo e($post['title']); ?></h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <a href="blog.php">Blog</a> <span>/</span> <span><?php echo e(truncateText($post['title'], 40)); ?></span>
        </nav>
    </div>
</section>

<!-- Blog Post Content -->
<section class="section blog-post-section">
    <div class="container">
        <article class="blog-post" data-animate="fadeInUp">
            <!-- Post Meta -->
            <div class="blog-post-meta">
                <?php if (!empty($post['category'])): ?>
                <span class="blog-post-category"><i class="fas fa-folder"></i> <?php echo e($post['category']); ?></span>
                <?php endif; ?>
                <span class="blog-post-author"><i class="fas fa-user"></i> <?php echo e($post['author'] ?? 'Admin'); ?></span>
                <span class="blog-post-date"><i class="fas fa-calendar-alt"></i> <?php echo formatDate($post['published_at']); ?></span>
            </div>

            <!-- Featured Image -->
            <?php if (!empty($post['featured_image'])): ?>
            <div class="blog-post-image">
                <img src="<?php echo e($post['featured_image']); ?>" alt="<?php echo e($post['title']); ?>" loading="lazy">
            </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="blog-post-content">
                <?php echo $post['content']; ?>
            </div>
        </article>

        <!-- Related Posts -->
        <?php if (!empty($relatedPosts)): ?>
        <div class="related-posts" data-animate="fadeInUp">
            <h3 class="related-posts-title">Related Articles</h3>
            <div class="blog-grid blog-grid-small">
                <?php foreach ($relatedPosts as $related): ?>
                <article class="blog-card">
                    <?php if (!empty($related['featured_image'])): ?>
                    <div class="blog-card-image">
                        <a href="post.php?slug=<?php echo e($related['slug']); ?>">
                            <img src="<?php echo e($related['featured_image']); ?>" alt="<?php echo e($related['title']); ?>" loading="lazy">
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="blog-card-content">
                        <h3 class="blog-card-title">
                            <a href="post.php?slug=<?php echo e($related['slug']); ?>"><?php echo e($related['title']); ?></a>
                        </h3>
                        <div class="blog-card-meta">
                            <span><i class="fas fa-calendar-alt"></i> <?php echo formatDate($related['published_at']); ?></span>
                        </div>
                        <p class="blog-card-excerpt"><?php echo e(truncateText(strip_tags($related['content']), 100)); ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
