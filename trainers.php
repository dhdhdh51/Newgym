<?php
/**
 * Trainers Page
 * Showcase of all active trainers
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'trainers';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';

// Query active trainers
$trainers = getActiveItems('trainers', 'id ASC');

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Our Trainers</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Trainers</span>
        </nav>
    </div>
</section>

<!-- Trainers Section -->
<section class="section trainers-detail-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Meet Our Expert Team</h2>
            <p class="section-subtitle">Certified Professionals With Years Of Real-World Experience</p>
        </div>
        <?php if (!empty($trainers)): ?>
        <div class="trainers-grid trainers-grid-large">
            <?php foreach ($trainers as $trainer): ?>
            <div class="trainer-card trainer-card-detailed" data-animate="fadeInUp">
                <div class="trainer-photo">
                    <?php if (!empty($trainer['photo'])): ?>
                    <img src="<?php echo e($trainer['photo']); ?>" alt="<?php echo e($trainer['name']); ?>" loading="lazy">
                    <?php else: ?>
                    <div class="trainer-photo-placeholder"><i class="fas fa-user"></i></div>
                    <?php endif; ?>
                </div>
                <div class="trainer-info">
                    <h3 class="trainer-name"><?php echo e($trainer['name']); ?></h3>
                    <p class="trainer-specialty"><i class="fas fa-star"></i> <?php echo e($trainer['specialty']); ?></p>
                    <p class="trainer-experience"><i class="fas fa-award"></i> <?php echo e($trainer['experience']); ?> Experience</p>
                    <?php if (!empty($trainer['bio'])): ?>
                    <p class="trainer-bio"><?php echo e($trainer['bio']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($trainer['instagram_link'])): ?>
                    <a href="<?php echo e($trainer['instagram_link']); ?>" class="trainer-social" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i> Follow on Instagram</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <p>Our trainer profiles are being updated. Check back soon to meet our expert team.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content" data-animate="fadeInUp">
            <h2>Want To Book Personal Training?</h2>
            <p>Get one-on-one attention from our certified trainers. Achieve your goals faster with personalized guidance.</p>
            <div class="cta-buttons">
                <a href="contact.php?service=Personal+Training" class="btn btn-primary btn-lg">Book Personal Training</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20want%20to%20book%20a%20personal%20training%20session." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
