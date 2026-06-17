<?php
/**
 * Gallery Page
 * Filterable photo gallery with lightbox
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'gallery';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();

// Query all active gallery items
$galleryItems = getActiveItems('gallery', 'id DESC');

// Get unique categories for filter
$categories = [];
foreach ($galleryItems as $item) {
    $cat = $item['category'] ?? 'general';
    if (!in_array($cat, $categories)) {
        $categories[] = $cat;
    }
}

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Gallery</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Gallery</span>
        </nav>
    </div>
</section>

<!-- Gallery Section -->
<section class="section gallery-detail-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Our Facilities</h2>
            <p class="section-subtitle">Take A Virtual Tour Of Our World-Class Gym</p>
        </div>

        <?php if (!empty($galleryItems)): ?>
        <!-- Category Filters -->
        <?php if (count($categories) > 1): ?>
        <div class="gallery-filters" data-animate="fadeInUp">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($categories as $category): ?>
            <button class="filter-btn" data-filter="<?php echo e($category); ?>"><?php echo e(ucfirst($category)); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Gallery Grid -->
        <div class="gallery-grid gallery-grid-full">
            <?php foreach ($galleryItems as $item): ?>
            <div class="gallery-item" data-category="<?php echo e($item['category'] ?? 'general'); ?>" data-animate="fadeInUp">
                <a href="<?php echo e($item['image']); ?>" class="gallery-lightbox" data-title="<?php echo e($item['title']); ?>">
                    <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-title"><?php echo e($item['title']); ?></span>
                        <span class="gallery-category"><?php echo e(ucfirst($item['category'] ?? 'general')); ?></span>
                        <i class="fas fa-search-plus gallery-zoom-icon"></i>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>Gallery photos are being uploaded. Check back soon to see our amazing facilities.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="lightbox-modal" id="lightboxModal">
    <button class="lightbox-close" id="lightboxClose" aria-label="Close lightbox">&times;</button>
    <button class="lightbox-prev" id="lightboxPrev" aria-label="Previous image"><i class="fas fa-chevron-left"></i></button>
    <button class="lightbox-next" id="lightboxNext" aria-label="Next image"><i class="fas fa-chevron-right"></i></button>
    <div class="lightbox-content">
        <img src="" alt="" id="lightboxImage">
        <p class="lightbox-caption" id="lightboxCaption"></p>
    </div>
</div>

<?php include 'footer.php'; ?>
