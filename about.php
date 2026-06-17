<?php
/**
 * About Us Page
 * Company story, mission, and why choose us
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'about';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();
$aboutContent = $allSettings['about_content'] ?? '';
$gymName = $allSettings['gym_name'] ?? 'Iron Pulse Fitness';
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">About Us</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>About Us</span>
        </nav>
    </div>
</section>

<!-- About Content Section -->
<section class="section about-detail-section">
    <div class="container">
        <div class="about-detail-content" data-animate="fadeInUp">
            <h2>Our Story</h2>
            <p><?php echo e($aboutContent); ?></p>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="section mission-section">
    <div class="container">
        <div class="mission-grid" data-animate="fadeInUp">
            <div class="mission-card">
                <div class="mission-icon"><i class="fas fa-bullseye"></i></div>
                <h3>Our Mission</h3>
                <p>To make premium fitness accessible to everyone by providing world-class facilities, expert guidance, and a supportive community that empowers individuals to achieve their health and fitness goals, regardless of their starting point.</p>
            </div>
            <div class="mission-card">
                <div class="mission-icon"><i class="fas fa-eye"></i></div>
                <h3>Our Vision</h3>
                <p>To become the most trusted fitness brand in the region by consistently delivering exceptional training experiences, fostering lasting lifestyle changes, and building a community where every member feels valued, supported, and inspired to push beyond their limits.</p>
            </div>
            <div class="mission-card">
                <div class="mission-icon"><i class="fas fa-heart"></i></div>
                <h3>Our Values</h3>
                <p>We believe in integrity, dedication, and continuous improvement. Every decision we make is guided by our commitment to member satisfaction, safety, and results. We treat every member like family and celebrate each milestone together.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section why-choose-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Why Choose <?php echo e($gymName); ?>?</h2>
            <p class="section-subtitle">Six Reasons That Set Us Apart From The Rest</p>
        </div>
        <div class="why-choose-grid">
            <div class="why-choose-card" data-animate="fadeInUp">
                <div class="why-choose-icon"><i class="fas fa-trophy"></i></div>
                <h3>Proven Track Record</h3>
                <p>Over 5000 members have transformed their bodies and lives with our expert guidance. Our results speak louder than words, with countless success stories to prove it.</p>
            </div>
            <div class="why-choose-card" data-animate="fadeInUp">
                <div class="why-choose-icon"><i class="fas fa-users-cog"></i></div>
                <h3>Expert Certified Trainers</h3>
                <p>Our trainers hold nationally recognized certifications and bring years of real-world experience in bodybuilding, functional training, sports conditioning, and rehabilitation.</p>
            </div>
            <div class="why-choose-card" data-animate="fadeInUp">
                <div class="why-choose-icon"><i class="fas fa-cogs"></i></div>
                <h3>Premium Equipment</h3>
                <p>We invest in the latest fitness technology and equipment from top brands like Life Fitness, Hammer Strength, and Technogym, ensuring you have the best tools for your workout.</p>
            </div>
            <div class="why-choose-card" data-animate="fadeInUp">
                <div class="why-choose-icon"><i class="fas fa-hand-holding-heart"></i></div>
                <h3>Personalized Approach</h3>
                <p>No two bodies are the same, and neither are our programs. Every member receives a customized workout and nutrition plan designed specifically for their body type and goals.</p>
            </div>
            <div class="why-choose-card" data-animate="fadeInUp">
                <div class="why-choose-icon"><i class="fas fa-building"></i></div>
                <h3>World-Class Facility</h3>
                <p>Our 10,000 sq ft air-conditioned facility features dedicated zones for cardio, strength, functional training, group classes, and recovery, ensuring a comfortable workout environment.</p>
            </div>
            <div class="why-choose-card" data-animate="fadeInUp">
                <div class="why-choose-icon"><i class="fas fa-rupee-sign"></i></div>
                <h3>Affordable Plans</h3>
                <p>Premium fitness does not have to break the bank. Our flexible membership plans are designed to offer maximum value at competitive prices, with no hidden charges.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content" data-animate="fadeInUp">
            <h2>Join The <?php echo e($gymName); ?> Family Today</h2>
            <p>Start your transformation with a free trial session and experience the difference for yourself.</p>
            <div class="cta-buttons">
                <a href="contact.php?trial=1" class="btn btn-primary btn-lg">Book Free Trial</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20want%20to%20know%20more%20about%20your%20gym." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
