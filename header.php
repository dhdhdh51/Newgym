<?php
/**
 * Header Include
 * HTML head, navigation, and sticky buttons
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/functions.php';
}

// Load all settings for performance
$allSettings = getAllSettings();

// Load SEO data for current page
$currentPage = $currentPage ?? 'home';
$seoData = getSeoForPage($currentPage);

// Determine meta values with fallbacks
$metaTitle = $seoData['meta_title'] ?? $allSettings['default_meta_title'] ?? 'Iron Pulse Fitness';
$metaDescription = $seoData['meta_description'] ?? $allSettings['default_meta_description'] ?? '';
$metaKeywords = $seoData['meta_keywords'] ?? $allSettings['default_meta_keywords'] ?? '';
$canonicalUrl = $seoData['canonical_url'] ?? '';
$robotsMeta = $seoData['robots_meta'] ?? 'index, follow';
$ogTitle = $seoData['og_title'] ?? $metaTitle;
$ogDescription = $seoData['og_description'] ?? $metaDescription;
$ogImage = $seoData['og_image'] ?? ($allSettings['logo'] ?? '');
$twitterTitle = $seoData['twitter_title'] ?? $ogTitle;
$twitterDescription = $seoData['twitter_description'] ?? $ogDescription;
$twitterImage = $seoData['twitter_image'] ?? $ogImage;
$schemaJson = $seoData['schema_json'] ?? '';
$customHeaderScripts = $seoData['custom_header_scripts'] ?? '';

// Site info
$gymName = $allSettings['gym_name'] ?? 'Iron Pulse Fitness';
$logo = $allSettings['logo'] ?? '';
$favicon = $allSettings['favicon'] ?? '';
$primaryColor = $allSettings['primary_color'] ?? '#e63946';
$secondaryColor = $allSettings['secondary_color'] ?? '#1d1d1d';
$buttonColor = $allSettings['button_color'] ?? '#e63946';
$whatsappNumber = $allSettings['whatsapp_number'] ?? '';
$phoneNumber = $allSettings['phone_number'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Primary Meta Tags -->
    <title><?php echo e($metaTitle); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>">
    <meta name="keywords" content="<?php echo e($metaKeywords); ?>">
    <meta name="robots" content="<?php echo e($robotsMeta); ?>">
    <meta name="author" content="<?php echo e($gymName); ?>">
    
    <?php if ($canonicalUrl): ?>
    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>">
    <?php endif; ?>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($ogTitle); ?>">
    <meta property="og:description" content="<?php echo e($ogDescription); ?>">
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?php echo e($gymName); ?>">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($twitterTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($twitterDescription); ?>">
    <?php if ($twitterImage): ?>
    <meta name="twitter:image" content="<?php echo e($twitterImage); ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <?php if ($favicon): ?>
    <link rel="icon" type="image/png" href="<?php echo e($favicon); ?>">
    <link rel="apple-touch-icon" href="<?php echo e($favicon); ?>">
    <?php endif; ?>
    
    <!-- CSS Variables from Settings -->
    <style>
        :root {
            --primary-color: <?php echo e($primaryColor); ?>;
            --secondary-color: <?php echo e($secondaryColor); ?>;
            --button-color: <?php echo e($buttonColor); ?>;
        }
    </style>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Schema.org JSON-LD -->
    <?php if ($schemaJson): ?>
    <script type="application/ld+json">
    <?php echo $schemaJson; ?>
    </script>
    <?php endif; ?>
    
    <!-- Google Analytics -->
    <?php if (!empty($allSettings['google_analytics'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($allSettings['google_analytics']); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e($allSettings['google_analytics']); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Search Console -->
    <?php if (!empty($allSettings['search_console'])): ?>
    <meta name="google-site-verification" content="<?php echo e($allSettings['search_console']); ?>">
    <?php endif; ?>
    
    <!-- Facebook Pixel -->
    <?php if (!empty($allSettings['facebook_pixel'])): ?>
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo e($allSettings['facebook_pixel']); ?>');
        fbq('track', 'PageView');
    </script>
    <?php endif; ?>
    
    <!-- Custom Header Scripts -->
    <?php if ($customHeaderScripts): ?>
    <?php echo $customHeaderScripts; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <header class="site-header" id="siteHeader">
        <nav class="navbar">
            <div class="container">
                <a href="index.php" class="nav-brand">
                    <?php if ($logo): ?>
                    <img src="<?php echo e($logo); ?>" alt="<?php echo e($gymName); ?>" class="nav-logo">
                    <?php endif; ?>
                    <span class="nav-brand-text"><?php echo e($gymName); ?></span>
                </a>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
                
                <div class="nav-menu" id="navMenu">
                    <ul class="nav-list">
                        <li class="nav-item"><a href="index.php" class="nav-link <?php echo $currentPage === 'home' ? 'active' : ''; ?>">Home</a></li>
                        <li class="nav-item"><a href="about.php" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">About</a></li>
                        <li class="nav-item"><a href="services.php" class="nav-link <?php echo $currentPage === 'services' ? 'active' : ''; ?>">Services</a></li>
                        <li class="nav-item"><a href="plans.php" class="nav-link <?php echo $currentPage === 'plans' ? 'active' : ''; ?>">Plans</a></li>
                        <li class="nav-item"><a href="trainers.php" class="nav-link <?php echo $currentPage === 'trainers' ? 'active' : ''; ?>">Trainers</a></li>
                        <li class="nav-item"><a href="classes.php" class="nav-link <?php echo $currentPage === 'classes' ? 'active' : ''; ?>">Classes</a></li>
                        <li class="nav-item"><a href="gallery.php" class="nav-link <?php echo $currentPage === 'gallery' ? 'active' : ''; ?>">Gallery</a></li>
                        <li class="nav-item"><a href="blog.php" class="nav-link <?php echo $currentPage === 'blog' ? 'active' : ''; ?>">Blog</a></li>
                        <li class="nav-item"><a href="contact.php" class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                    </ul>
                    <a href="contact.php?trial=1" class="btn btn-primary nav-cta">Book Free Trial</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sticky WhatsApp Button -->
    <?php if ($whatsappNumber): ?>
    <a href="https://wa.me/<?php echo e($whatsappNumber); ?>?text=Hi%2C%20I%20am%20interested%20in%20joining%20your%20gym.%20Please%20share%20the%20details." class="sticky-whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>
    
    <!-- Sticky Call Button (Mobile) -->
    <?php if ($phoneNumber): ?>
    <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phoneNumber)); ?>" class="sticky-call" aria-label="Call us">
        <i class="fas fa-phone-alt"></i>
    </a>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
