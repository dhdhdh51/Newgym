<?php
/**
 * Membership Plans Page
 * Display all active membership plans with pricing
 */
require_once 'config.php';
require_once 'functions.php';

$currentPage = 'plans';
$seo = getSeoForPage($currentPage);

$allSettings = getAllSettings();
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';

// Query active plans
$plans = getActiveItems('membership_plans', 'id ASC');

include 'header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Membership Plans</h1>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Membership Plans</span>
        </nav>
    </div>
</section>

<!-- Plans Section -->
<section class="section plans-detail-section">
    <div class="container">
        <div class="section-header" data-animate="fadeInUp">
            <h2 class="section-title">Choose Your Perfect Plan</h2>
            <p class="section-subtitle">Flexible Options To Match Your Fitness Goals And Budget</p>
        </div>
        <?php if (!empty($plans)): ?>
        <div class="plans-grid">
            <?php foreach ($plans as $plan): ?>
            <div class="plan-card <?php echo $plan['is_highlighted'] ? 'plan-highlighted' : ''; ?>" data-animate="fadeInUp">
                <?php if ($plan['is_highlighted']): ?>
                <div class="plan-badge">Most Popular</div>
                <?php endif; ?>
                <h3 class="plan-name"><?php echo e($plan['plan_name']); ?></h3>
                
                <!-- Monthly Price -->
                <div class="plan-price">
                    <span class="price-currency">&#8377;</span>
                    <span class="price-amount"><?php echo number_format($plan['monthly_price'], 0); ?></span>
                    <span class="price-period">/month</span>
                </div>
                
                <!-- Quarterly and Yearly Pricing -->
                <div class="plan-pricing-options">
                    <div class="pricing-option">
                        <span class="pricing-label">Quarterly</span>
                        <span class="pricing-value">&#8377;<?php echo number_format($plan['quarterly_price'], 0); ?></span>
                        <span class="pricing-save">Save &#8377;<?php echo number_format(($plan['monthly_price'] * 3) - $plan['quarterly_price'], 0); ?></span>
                    </div>
                    <div class="pricing-option">
                        <span class="pricing-label">Yearly</span>
                        <span class="pricing-value">&#8377;<?php echo number_format($plan['yearly_price'], 0); ?></span>
                        <span class="pricing-save">Save &#8377;<?php echo number_format(($plan['monthly_price'] * 12) - $plan['yearly_price'], 0); ?></span>
                    </div>
                </div>
                
                <!-- Features List -->
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
                
                <!-- CTA Buttons -->
                <div class="plan-cta">
                    <a href="contact.php?plan=<?php echo urlencode($plan['plan_name']); ?>" class="btn <?php echo $plan['is_highlighted'] ? 'btn-primary' : 'btn-outline'; ?> btn-block">Enquire Now</a>
                    <?php if ($whatsappNumber): ?>
                    <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20am%20interested%20in%20the%20<?php echo urlencode($plan['plan_name']); ?>%20plan.%20Please%20share%20details." class="btn btn-whatsapp btn-sm btn-block" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>Membership plans are being updated. Please contact us for current pricing.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- FAQ-like info -->
<section class="section plans-info-section">
    <div class="container">
        <div class="plans-info-grid" data-animate="fadeInUp">
            <div class="plans-info-card">
                <i class="fas fa-shield-alt"></i>
                <h4>No Hidden Charges</h4>
                <p>What you see is what you pay. No registration fees, no maintenance charges, no surprises.</p>
            </div>
            <div class="plans-info-card">
                <i class="fas fa-sync-alt"></i>
                <h4>Easy Plan Upgrade</h4>
                <p>Start with any plan and upgrade anytime. The difference will be adjusted pro-rata.</p>
            </div>
            <div class="plans-info-card">
                <i class="fas fa-pause-circle"></i>
                <h4>Freeze Option</h4>
                <p>Need a break? Freeze your membership for up to 30 days per year at no extra cost.</p>
            </div>
            <div class="plans-info-card">
                <i class="fas fa-gift"></i>
                <h4>Free Trial</h4>
                <p>Not sure which plan suits you? Try our facilities with a complimentary 1-day trial pass.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content" data-animate="fadeInUp">
            <h2>Still Have Questions About Our Plans?</h2>
            <p>Our team is happy to help you choose the right plan. Get in touch today.</p>
            <div class="cta-buttons">
                <a href="contact.php?trial=1" class="btn btn-primary btn-lg">Book Free Trial</a>
                <?php if ($whatsappNumber): ?>
                <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20need%20help%20choosing%20a%20membership%20plan." class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> Ask on WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
