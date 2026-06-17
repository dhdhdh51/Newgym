<?php
/**
 * Classes Page
 * Display class schedule and details
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'classes';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';

// Query active classes
$classes = getActiveItems('classes', 'id ASC');

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Class Schedule</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Classes</span>
        </nav>
    </div>
</section>

<!-- Classes Section -->
<section class="section classes-detail-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Our Group Fitness Classes</h2>
            <p class="section-subtitle">High-Energy Sessions To Keep You Motivated And On Track</p>
        </div>
        <?php if (!empty($classes)): ?>
        <div class="classes-grid classes-grid-detailed">
            <?php foreach ($classes as $class): ?>
            <div class="class-card class-card-full" data-animate="fadeInUp">
                <?php if (!empty($class['image'])): ?>
                <div class="class-image">
                    <img src="<?php echo e($class['image']); ?>" alt="<?php echo e($class['class_name']); ?>" loading="lazy">
                </div>
                <?php endif; ?>
                <div class="class-info">
                    <h3 class="class-name"><?php echo e($class['class_name']); ?></h3>
                    <div class="class-meta">
                        <p class="class-trainer"><i class="fas fa-user"></i> <?php echo e($class['trainer_name']); ?></p>
                        <p class="class-time"><i class="fas fa-clock"></i> <?php echo e($class['class_time']); ?></p>
                        <p class="class-days"><i class="fas fa-calendar-alt"></i> <?php echo e($class['class_days']); ?></p>
                        <p class="class-duration"><i class="fas fa-hourglass-half"></i> <?php echo e($class['duration']); ?></p>
                    </div>
                    <?php if (!empty($class['description'])): ?>
                    <p class="class-description"><?php echo e($class['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-alt"></i>
            <p>Class schedule is being updated. Please check back soon or contact us for the latest schedule.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content" data-animate="fadeInUp">
            <h2>Want To Join A Class?</h2>
            <p>All group classes are included with Standard and Premium memberships. Basic members can attend 2 classes per week.</p>
            <div class="cta-buttons">
                <a href="plans.php" class="btn btn-primary btn-lg">View Membership Plans</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20want%20to%20join%20a%20group%20class.%20Please%20share%20details." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
