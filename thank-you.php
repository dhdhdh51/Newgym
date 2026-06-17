<?php
/**
 * Thank You Page
 * Success page after form submission
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'contact';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';
$phoneNumber = $allSettings['phone_number'] ?? '';
$gymName = $allSettings['gym_name'] ?? 'Iron Pulse Fitness';

include 'header.php';
?>

<!-- Thank You Section -->
<section class="section thank-you-section">
    <div class="container">
        <div class="thank-you-content" data-animate="fadeInUp">
            <div class="thank-you-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Thank You!</h1>
            <h2>Your Message Has Been Received</h2>
            <p>We appreciate you reaching out to <?php echo e($gymName); ?>. Our team will review your enquiry and get back to you within 24 hours.</p>
            <p>In the meantime, feel free to reach us directly via phone or WhatsApp for an instant response.</p>
            <div class="thank-you-buttons">
                <a href="index.php" class="btn btn-primary btn-lg"><i class="fas fa-home"></i> Back to Home</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20just%20submitted%20an%20enquiry%20on%20your%20website." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
                <?php endif; ?>
                <?php if ($phoneNumber): ?>
                <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phoneNumber)); ?>" class="btn btn-outline btn-lg"><i class="fas fa-phone-alt"></i> Call Us</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
