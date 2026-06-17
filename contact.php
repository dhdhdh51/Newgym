<?php
/**
 * Contact Page
 * Contact information, map, and contact form
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'contact';
$seo = getSeoForPage($currentPage);

// Process Contact Form
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');

        if (empty($name) || empty($phone)) {
            $error = 'Name and Phone are required fields.';
        } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO enquiries (enquiry_type, name, phone, email, message) VALUES (:type, :name, :phone, :email, :message)");
                $stmt->execute([
                    ':type' => 'contact',
                    ':name' => $name,
                    ':phone' => $phone,
                    ':email' => $email,
                    ':message' => $message
                ]);
                redirect('thank-you.php');
            } catch (PDOException $e) {
                $error = 'Something went wrong. Please try again later.';
            }
        }
    }
}

$allSettings = getAllSettings();
$address = $allSettings['address'] ?? '';
$phoneNumber = $allSettings['phone_number'] ?? '';
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';
$email_addr = $allSettings['email'] ?? '';
$businessHours = $allSettings['business_hours'] ?? '';
$googleMapEmbed = $allSettings['google_map_embed'] ?? '';

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Contact Us</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Contact</span>
        </nav>
    </div>
</section>

<!-- Contact Section -->
<section class="section contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="contact-info" data-animate="fadeInUp">
                <h2>Get In Touch</h2>
                <p>We would love to hear from you. Whether you have a question about our membership plans, classes, or anything else, our team is ready to answer all your questions.</p>

                <div class="contact-info-list">
                    <?php if ($address): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h4>Our Address</h4>
                            <p><?php echo e($address); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($phoneNumber): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h4>Phone Number</h4>
                            <p><a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phoneNumber)); ?>"><?php echo e($phoneNumber); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($whatsappNumber): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fab fa-whatsapp"></i></div>
                        <div>
                            <h4>WhatsApp</h4>
                            <p><a href="https://wa.me/<?php echo e($whatsappNumber); ?>" target="_blank" rel="noopener noreferrer">+<?php echo e($whatsappNumber); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($email_addr): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h4>Email Address</h4>
                            <p><a href="mailto:<?php echo e($email_addr); ?>"><?php echo e($email_addr); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($businessHours): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <h4>Business Hours</h4>
                            <p><?php echo e($businessHours); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrapper" data-animate="fadeInUp">
                <h2>Send Us A Message</h2>
                <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="contact.php" class="form contact-form" id="contactForm">
                    <input type="hidden" name="csrf_token" value="<?php echo e(generateCsrfToken()); ?>">
                    <input type="hidden" name="contact_submit" value="1">
                    <div class="form-group">
                        <label for="contact_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="contact_name" name="name" required placeholder="Enter your full name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contact_phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="contact_phone" name="phone" required placeholder="Enter your phone number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contact_email">Email Address</label>
                        <input type="email" id="contact_email" name="email" placeholder="Enter your email address" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contact_message">Your Message <span class="required">*</span></label>
                        <textarea id="contact_message" name="message" rows="5" required placeholder="How can we help you?" class="form-control"></textarea>
                    </div>
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Google Map Section -->
<?php if ($googleMapEmbed): ?>
<section class="section map-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Find Us On The Map</h2>
        </div>
        <div class="map-wrapper" data-animate="fadeInUp">
            <?php echo $googleMapEmbed; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
