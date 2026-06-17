<?php
/**
 * Blog Listing Page
 * Display published blog posts
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'blog';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();

// Get published posts
$posts = getPublishedPosts(20, 0);

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Blog</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Blog</span>
        </nav>
    </div>
</section>

<!-- Blog Listing Section -->
<section class="section blog-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Fitness Tips & Insights</h2>
            <p class="section-subtitle">Expert Articles On Training, Nutrition, And Wellness</p>
        </div>
        <?php if (!empty($posts)): ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
            <article class="blog-card" data-animate="fadeInUp">
                <?php if (!empty($post['featured_image'])): ?>
                <div class="blog-card-image">
                    <a href="post.php?slug=<?php echo e($post['slug']); ?>">
                        <img src="<?php echo e($post['featured_image']); ?>" alt="<?php echo e($post['title']); ?>" loading="lazy">
                    </a>
                </div>
                <?php endif; ?>
                <div class="blog-card-content">
                    <?php if (!empty($post['category'])): ?>
                    <span class="blog-card-category"><?php echo e($post['category']); ?></span>
                    <?php endif; ?>
                    <h3 class="blog-card-title">
                        <a href="post.php?slug=<?php echo e($post['slug']); ?>"><?php echo e($post['title']); ?></a>
                    </h3>
                    <div class="blog-card-meta">
                        <span><i class="fas fa-user"></i> <?php echo e($post['author'] ?? 'Admin'); ?></span>
                        <span><i class="fas fa-calendar-alt"></i> <?php echo formatDate($post['published_at']); ?></span>
                    </div>
                    <p class="blog-card-excerpt"><?php echo e(truncateText(strip_tags($post['content']), 150)); ?></p>
                    <a href="post.php?slug=<?php echo e($post['slug']); ?>" class="blog-card-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-blog"></i>
            <h3>No Posts Yet</h3>
            <p>We are working on exciting fitness content for you. Check back soon for expert tips, workout guides, and nutrition advice.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
