<?php
/**
 * Footer Include
 * Site footer with 4 columns, copyright, and scripts
 */

if (!isset($allSettings)) {
    $allSettings = getAllSettings();
}

$gymName = $allSettings['gym_name'] ?? 'Iron Pulse Fitness';
$footerText = $allSettings['footer_text'] ?? '';
$address = $allSettings['address'] ?? '';
$phoneNumber = $allSettings['phone_number'] ?? '';
$email = $allSettings['email'] ?? '';
$businessHours = $allSettings['business_hours'] ?? '';
$instagramLink = $allSettings['instagram_link'] ?? '';
$facebookLink = $allSettings['facebook_link'] ?? '';
$youtubeLink = $allSettings['youtube_link'] ?? '';

// Get custom footer scripts from SEO
$currentPage = $currentPage ?? 'home';
$footerSeo = getSeoForPage($currentPage);
$customFooterScripts = $footerSeo['custom_footer_scripts'] ?? '';
?>
    </main>
    <!-- End Main Content -->

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Column 1: About -->
                <div class="footer-col">
                    <h3 class="footer-title"><?php echo e($gymName); ?></h3>
                    <p class="footer-text"><?php echo e($footerText); ?></p>
                    <div class="footer-social">
                        <?php if ($instagramLink): ?>
                        <a href="<?php echo e($instagramLink); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if ($facebookLink): ?>
                        <a href="<?php echo e($facebookLink); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if ($youtubeLink): ?>
                        <a href="<?php echo e($youtubeLink); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="footer-col">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="plans.php">Membership Plans</a></li>
                        <li><a href="trainers.php">Our Trainers</a></li>
                        <li><a href="classes.php">Class Schedule</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact Info -->
                <div class="footer-col">
                    <h3 class="footer-title">Contact Info</h3>
                    <ul class="footer-contact">
                        <?php if ($address): ?>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo e($address); ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($phoneNumber): ?>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phoneNumber)); ?>"><?php echo e($phoneNumber); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if ($email): ?>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo e($email); ?>"><?php echo e($email); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if ($businessHours): ?>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><?php echo e($businessHours); ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Column 4: Newsletter / Social -->
                <div class="footer-col">
                    <h3 class="footer-title">Get In Touch</h3>
                    <p class="footer-text">Ready to start your fitness journey? Contact us today for a free consultation and gym tour.</p>
                    <a href="contact.php?trial=1" class="btn btn-primary btn-sm">Book Free Trial</a>
                    <div class="footer-hours">
                        <h4>Business Hours</h4>
                        <p><?php echo e($businessHours); ?></p>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo e($gymName); ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTopBtn" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Main JavaScript -->
    <script src="assets/js/main.js"></script>

    <!-- Custom Footer Scripts -->
    <?php if ($customFooterScripts): ?>
    <?php echo $customFooterScripts; ?>
    <?php endif; ?>
</body>
</html>
