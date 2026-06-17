<?php
/**
 * Services Page
 * Listing of all gym services
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'services';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();
$services = json_decode($allSettings['services'] ?? '[]', true) ?: [];
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Our Services</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Services</span>
        </nav>
    </div>
</section>

<!-- Services Listing -->
<section class="section services-detail-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">What We Offer</h2>
            <p class="section-subtitle">Comprehensive Fitness Solutions Tailored To Your Needs</p>
        </div>
        <?php if (!empty($services)): ?>
        <div class="services-grid services-grid-large">
            <?php foreach ($services as $service): ?>
            <div class="service-card service-card-detailed" data-animate="fadeInUp">
                <div class="service-icon-large">
                    <i class="fas <?php echo e($service['icon'] ?? 'fa-dumbbell'); ?>"></i>
                </div>
                <h3 class="service-title"><?php echo e($service['title'] ?? ''); ?></h3>
                <p class="service-description"><?php echo e($service['description'] ?? ''); ?></p>
                <a href="contact.php?service=<?php echo urlencode($service['title'] ?? ''); ?>" class="btn btn-sm btn-outline">Enquire Now</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-dumbbell"></i>
            <p>Our services information is being updated. Please check back soon or contact us directly.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content" data-animate="fadeInUp">
            <h2>Ready To Get Started?</h2>
            <p>Contact us today to learn more about our services and find the perfect program for your fitness goals.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20want%20to%20enquire%20about%20your%20services." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
