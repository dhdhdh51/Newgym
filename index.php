<?php
/**
 * Home Page
 * Main landing page with all sections
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'home';
$seo = getSeoForPage($currentPage);

// Process Free Trial Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['free_trial_submit'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $whatsapp = sanitizeInput($_POST['whatsapp'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $age = (int)($_POST['age'] ?? 0);
        $gender = sanitizeInput($_POST['gender'] ?? '');
        $fitness_goal = sanitizeInput($_POST['fitness_goal'] ?? '');
        $preferred_time = sanitizeInput($_POST['preferred_time'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');

        if (empty($name) || empty($phone)) {
            $error = 'Name and Phone are required fields.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO enquiries (enquiry_type, name, phone, whatsapp, email, age, gender, fitness_goal, preferred_time, message) VALUES (:type, :name, :phone, :whatsapp, :email, :age, :gender, :fitness_goal, :preferred_time, :message)");
                $stmt->execute([
                    ':type' => 'free_trial',
                    ':name' => $name,
                    ':phone' => $phone,
                    ':whatsapp' => $whatsapp,
                    ':email' => $email,
                    ':age' => $age > 0 ? $age : null,
                    ':gender' => in_array($gender, ['male', 'female', 'other']) ? $gender : null,
                    ':fitness_goal' => $fitness_goal,
                    ':preferred_time' => $preferred_time,
                    ':message' => $message
                ]);
                redirect('thank-you.php');
            } catch (PDOException $e) {
                $error = 'Something went wrong. Please try again later.';
            }
        }
    }
}

// Load data for sections
$allSettings = getAllSettings();
$heroHeading = $allSettings['hero_heading'] ?? 'Build Your Dream Body With Expert Training';
$heroSubheading = $allSettings['hero_subheading'] ?? '';
$heroImage = $allSettings['hero_image'] ?? '';
$aboutContent = $allSettings['about_content'] ?? '';
$services = json_decode($allSettings['services'] ?? '[]', true) ?: [];
$faqs = json_decode($allSettings['faqs'] ?? '[]', true) ?: [];
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';

// Query database sections
$plans = getActiveItems('membership_plans', 'id ASC');
$trainers = getActiveItems('trainers', 'id ASC');
$classes = getActiveItems('classes', 'id ASC');
$testimonials = getActiveItems('testimonials', 'id ASC');
$transformations = getActiveItems('transformations', 'id ASC');

// Gallery - last 8
try {
    $stmt = $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY id DESC LIMIT 8");
    $galleryItems = $stmt->fetchAll();
} catch (PDOException $e) {
    $galleryItems = [];
}

include 'header.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('<?php echo e($heroImage); ?>');">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content" data-animate="fadeInUp">
            <h1 class="hero-title"><?php echo e($heroHeading); ?></h1>
            <p class="hero-subtitle"><?php echo e($heroSubheading); ?></p>
            <div class="hero-buttons">
                <a href="#free-trial" class="btn btn-primary btn-lg">Book Free Trial</a>
                <a href="plans.php" class="btn btn-outline btn-lg">View Plans</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20am%20interested%20in%20joining%20your%20gym." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Trust Badges Section -->
<section class="trust-badges-section">
    <div class="container">
        <div class="trust-badges-grid">
            <div class="trust-badge" data-animate="fadeInUp">
                <div class="trust-badge-icon"><i class="fas fa-certificate"></i></div>
                <h4>Certified Trainers</h4>
                <p>All trainers are nationally certified professionals</p>
            </div>
            <div class="trust-badge" data-animate="fadeInUp">
                <div class="trust-badge-icon"><i class="fas fa-dumbbell"></i></div>
                <h4>Modern Equipment</h4>
                <p>Latest machines from top international brands</p>
            </div>
            <div class="trust-badge" data-animate="fadeInUp">
                <div class="trust-badge-icon"><i class="fas fa-user-check"></i></div>
                <h4>Personal Training</h4>
                <p>One-on-one sessions tailored to your goals</p>
            </div>
            <div class="trust-badge" data-animate="fadeInUp">
                <div class="trust-badge-icon"><i class="fas fa-calendar-alt"></i></div>
                <h4>Flexible Membership</h4>
                <p>Monthly, quarterly, and yearly plans available</p>
            </div>
            <div class="trust-badge" data-animate="fadeInUp">
                <div class="trust-badge-icon"><i class="fas fa-apple-alt"></i></div>
                <h4>Diet Guidance</h4>
                <p>Customized nutrition plans from certified experts</p>
            </div>
            <div class="trust-badge" data-animate="fadeInUp">
                <div class="trust-badge-icon"><i class="fas fa-gift"></i></div>
                <h4>Free Trial Available</h4>
                <p>Experience our facilities before committing</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section about-section" id="about">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">About Us</h2>
            <p class="section-subtitle">Your Fitness Journey Starts Here</p>
        </div>
        <div class="about-content" data-animate="fadeInUp">
            <p><?php echo e($aboutContent); ?></p>
            <a href="about.php" class="btn btn-primary">Learn More About Us</a>
        </div>
    </div>
</section>

<!-- Services Section -->
<?php if (!empty($services)): ?>
<section class="section services-section" id="services">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Comprehensive Fitness Solutions For Everyone</p>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <div class="service-card" data-animate="fadeInUp">
                <div class="service-icon">
                    <i class="fas <?php echo e($service['icon'] ?? 'fa-dumbbell'); ?>"></i>
                </div>
                <h3 class="service-title"><?php echo e($service['title'] ?? ''); ?></h3>
                <p class="service-description"><?php echo e($service['description'] ?? ''); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="services.php" class="btn btn-outline">View All Services</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Membership Plans Section -->
<?php if (!empty($plans)): ?>
<section class="section plans-section" id="plans">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Membership Plans</h2>
            <p class="section-subtitle">Choose The Perfect Plan For Your Fitness Goals</p>
        </div>
        <div class="plans-grid">
            <?php foreach ($plans as $plan): ?>
            <div class="plan-card <?php echo $plan['is_highlighted'] ? 'plan-highlighted' : ''; ?>" data-animate="fadeInUp">
                <?php if ($plan['is_highlighted']): ?>
                <div class="plan-badge">Most Popular</div>
                <?php endif; ?>
                <h3 class="plan-name"><?php echo e($plan['plan_name']); ?></h3>
                <div class="plan-price">
                    <span class="price-currency">&#8377;</span>
                    <span class="price-amount"><?php echo number_format($plan['monthly_price'], 0); ?></span>
                    <span class="price-period">/month</span>
                </div>
                <div class="plan-prices-alt">
                    <span>&#8377;<?php echo number_format($plan['quarterly_price'], 0); ?>/quarter</span>
                    <span>&#8377;<?php echo number_format($plan['yearly_price'], 0); ?>/year</span>
                </div>
                <?php
                $features = json_decode($plan['features'], true) ?: [];
                if (!empty($features)):
                ?>
                <ul class="plan-features">
                    <?php foreach ($features as $feature): ?>
                    <li><i class="fas fa-check"></i> <?php echo e($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <a href="contact.php?plan=<?php echo urlencode($plan['plan_name']); ?>" class="btn <?php echo $plan['is_highlighted'] ? 'btn-primary' : 'btn-outline'; ?> btn-block">Enquire Now</a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="plans.php" class="btn btn-outline">View All Plans</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Trainers Section -->
<?php if (!empty($trainers)): ?>
<section class="section trainers-section" id="trainers">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Our Expert Trainers</h2>
            <p class="section-subtitle">Certified Professionals Dedicated To Your Success</p>
        </div>
        <div class="trainers-grid">
            <?php foreach ($trainers as $trainer): ?>
            <div class="trainer-card" data-animate="fadeInUp">
                <div class="trainer-photo">
                    <?php if (!empty($trainer['photo'])): ?>
                    <img src="<?php echo e($trainer['photo']); ?>" alt="<?php echo e($trainer['name']); ?>" loading="lazy">
                    <?php else: ?>
                    <div class="trainer-photo-placeholder"><i class="fas fa-user"></i></div>
                    <?php endif; ?>
                </div>
                <div class="trainer-info">
                    <h3 class="trainer-name"><?php echo e($trainer['name']); ?></h3>
                    <p class="trainer-specialty"><?php echo e($trainer['specialty']); ?></p>
                    <p class="trainer-experience"><i class="fas fa-award"></i> <?php echo e($trainer['experience']); ?></p>
                    <?php if (!empty($trainer['instagram_link'])): ?>
                    <a href="<?php echo e($trainer['instagram_link']); ?>" class="trainer-social" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="trainers.php" class="btn btn-outline">Meet All Trainers</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Classes Section -->
<?php if (!empty($classes)): ?>
<section class="section classes-section" id="classes">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Group Classes</h2>
            <p class="section-subtitle">Fun, Energizing Sessions For Every Fitness Level</p>
        </div>
        <div class="classes-grid">
            <?php foreach (array_slice($classes, 0, 6) as $class): ?>
            <div class="class-card" data-animate="fadeInUp">
                <div class="class-info">
                    <h3 class="class-name"><?php echo e($class['class_name']); ?></h3>
                    <p class="class-trainer"><i class="fas fa-user"></i> <?php echo e($class['trainer_name']); ?></p>
                    <p class="class-time"><i class="fas fa-clock"></i> <?php echo e($class['class_time']); ?></p>
                    <p class="class-days"><i class="fas fa-calendar-alt"></i> <?php echo e($class['class_days']); ?></p>
                    <p class="class-duration"><i class="fas fa-hourglass-half"></i> <?php echo e($class['duration']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="classes.php" class="btn btn-outline">View Full Schedule</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Preview Section -->
<?php if (!empty($galleryItems)): ?>
<section class="section gallery-section" id="gallery">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Our Gallery</h2>
            <p class="section-subtitle">A Glimpse Into Our World-Class Facilities</p>
        </div>
        <div class="gallery-grid">
            <?php foreach ($galleryItems as $item): ?>
            <div class="gallery-item" data-animate="fadeInUp">
                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy">
                <div class="gallery-overlay">
                    <span class="gallery-title"><?php echo e($item['title']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="gallery.php" class="btn btn-outline">View Full Gallery</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Transformations Section -->
<?php if (!empty($transformations)): ?>
<section class="section transformations-section" id="transformations">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Member Transformations</h2>
            <p class="section-subtitle">Real People, Real Results</p>
        </div>
        <div class="transformations-grid">
            <?php foreach ($transformations as $transformation): ?>
            <div class="transformation-card" data-animate="fadeInUp">
                <div class="transformation-images">
                    <div class="transformation-before">
                        <img src="<?php echo e($transformation['before_image']); ?>" alt="Before - <?php echo e($transformation['member_name']); ?>" loading="lazy">
                        <span class="transformation-label">Before</span>
                    </div>
                    <div class="transformation-after">
                        <img src="<?php echo e($transformation['after_image']); ?>" alt="After - <?php echo e($transformation['member_name']); ?>" loading="lazy">
                        <span class="transformation-label">After</span>
                    </div>
                </div>
                <div class="transformation-info">
                    <h3><?php echo e($transformation['member_name']); ?></h3>
                    <p class="transformation-duration"><i class="fas fa-clock"></i> <?php echo e($transformation['duration']); ?></p>
                    <?php if (!empty($transformation['description'])): ?>
                    <p class="transformation-desc"><?php echo e($transformation['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<?php if (!empty($testimonials)): ?>
<section class="section testimonials-section" id="testimonials">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">What Our Members Say</h2>
            <p class="section-subtitle">Trusted By Thousands Of Fitness Enthusiasts</p>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-card" data-animate="fadeInUp">
                <div class="testimonial-rating">
                    <?php echo renderStars((int)$testimonial['rating']); ?>
                </div>
                <p class="testimonial-text"><?php echo e($testimonial['review']); ?></p>
                <div class="testimonial-author">
                    <?php if (!empty($testimonial['photo'])): ?>
                    <img src="<?php echo e($testimonial['photo']); ?>" alt="<?php echo e($testimonial['client_name']); ?>" class="testimonial-photo" loading="lazy">
                    <?php else: ?>
                    <div class="testimonial-photo-placeholder"><i class="fas fa-user"></i></div>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo e($testimonial['client_name']); ?></strong>
                        <?php if (!empty($testimonial['city'])): ?>
                        <span><?php echo e($testimonial['city']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Free Trial CTA Section -->
<section class="section free-trial-section" id="free-trial">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Book Your Free Trial</h2>
            <p class="section-subtitle">Experience Our World-Class Facilities Before You Commit</p>
        </div>
        <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>
        <div class="free-trial-form-wrapper" data-animate="fadeInUp">
            <form method="POST" action="index.php#free-trial" class="form free-trial-form" id="freeTrialForm">
                <input type="hidden" name="csrf_token" value="<?php echo e(generateCsrfToken()); ?>">
                <input type="hidden" name="free_trial_submit" value="1">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="ft_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="ft_name" name="name" required placeholder="Enter your full name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ft_phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="ft_phone" name="phone" required placeholder="Enter your phone number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ft_whatsapp">WhatsApp Number</label>
                        <input type="tel" id="ft_whatsapp" name="whatsapp" placeholder="WhatsApp number (if different)" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ft_email">Email Address</label>
                        <input type="email" id="ft_email" name="email" placeholder="Enter your email address" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ft_age">Age</label>
                        <input type="number" id="ft_age" name="age" min="14" max="80" placeholder="Your age" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ft_gender">Gender</label>
                        <select id="ft_gender" name="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ft_fitness_goal">Fitness Goal</label>
                        <input type="text" id="ft_fitness_goal" name="fitness_goal" placeholder="e.g., Weight Loss, Muscle Gain" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ft_preferred_time">Preferred Time</label>
                        <select id="ft_preferred_time" name="preferred_time" class="form-control">
                            <option value="">Select Preferred Time</option>
                            <option value="Early Morning (5AM-7AM)">Early Morning (5AM-7AM)</option>
                            <option value="Morning (7AM-10AM)">Morning (7AM-10AM)</option>
                            <option value="Afternoon (10AM-4PM)">Afternoon (10AM-4PM)</option>
                            <option value="Evening (4PM-8PM)">Evening (4PM-8PM)</option>
                            <option value="Night (8PM-11PM)">Night (8PM-11PM)</option>
                        </select>
                    </div>
                    <div class="form-group form-group-full">
                        <label for="ft_message">Message (Optional)</label>
                        <textarea id="ft_message" name="message" rows="3" placeholder="Any specific requirements or questions?" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-submit">
                    <button type="submit" class="btn btn-primary btn-lg">Book My Free Trial</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<?php if (!empty($faqs)): ?>
<section class="section faq-section" id="faq">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Got Questions? We Have Answers</p>
        </div>
        <div class="faq-accordion" data-animate="fadeInUp">
            <?php foreach ($faqs as $index => $faq): ?>
            <div class="faq-item">
                <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-<?php echo $index; ?>">
                    <span><?php echo e($faq['question'] ?? ''); ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer" id="faq-answer-<?php echo $index; ?>">
                    <p><?php echo e($faq['answer'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Final CTA Section -->
<section class="section final-cta-section">
    <div class="container">
        <div class="final-cta-content" data-animate="fadeInUp">
            <h2>Ready To Start Your Fitness Journey?</h2>
            <p>Take the first step today. Our team is ready to help you achieve your goals.</p>
            <div class="final-cta-buttons">
                <?php $phoneNum = $allSettings['phone_number'] ?? ''; ?>
                <?php if ($phoneNum): ?>
                <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phoneNum)); ?>" class="btn btn-primary btn-lg"><i class="fas fa-phone-alt"></i> Call Now</a>
                <?php endif; ?>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20want%20to%20join%20your%20gym.%20Please%20share%20details." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Now</a>
                <?php endif; ?>
                <a href="#free-trial" class="btn btn-outline btn-lg">Book Free Trial</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
