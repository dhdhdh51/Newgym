<?php
/**
 * Admin Site Settings
 * Manages all website settings organized in sections
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Site Settings';
$success = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            // Text/textarea settings
            $textFields = [
                'gym_name', 'tagline', 'hero_heading', 'hero_subheading', 'about_content',
                'primary_color', 'secondary_color', 'button_color',
                'whatsapp_number', 'phone_number', 'email', 'address', 'google_map_embed',
                'business_hours', 'instagram_link', 'facebook_link', 'youtube_link',
                'footer_text', 'google_analytics', 'search_console', 'facebook_pixel',
                'default_meta_title', 'default_meta_description', 'default_meta_keywords',
                'services', 'faqs'
            ];

            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value2");

            foreach ($textFields as $field) {
                if (isset($_POST[$field])) {
                    $value = $_POST[$field];
                    $stmt->execute([':key' => $field, ':value' => $value, ':value2' => $value]);
                }
            }

            // File uploads
            $fileFields = ['logo', 'favicon', 'hero_image'];
            foreach ($fileFields as $fileField) {
                if (isset($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK) {
                    $uploadedPath = uploadImage($_FILES[$fileField], 'uploads/');
                    if ($uploadedPath) {
                        $stmt->execute([':key' => $fileField, ':value' => $uploadedPath, ':value2' => $uploadedPath]);
                    }
                }
            }

            $success = 'Settings updated successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to update settings. Please try again.';
        }
    }
}

// Get all current settings
$settings = getAllSettings();

include 'includes/admin-header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

    <!-- Brand Section -->
    <div class="admin-card">
        <h3 class="section-title">Brand Settings</h3>
        <div class="form-group">
            <label for="gym_name">Gym Name</label>
            <input type="text" id="gym_name" name="gym_name" value="<?php echo e($settings['gym_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="tagline">Tagline</label>
            <input type="text" id="tagline" name="tagline" value="<?php echo e($settings['tagline'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="logo">Logo (Upload new to replace)</label>
            <?php if (!empty($settings['logo'])): ?>
                <p style="margin-bottom:5px;"><small>Current: <?php echo e($settings['logo']); ?></small></p>
            <?php endif; ?>
            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="favicon">Favicon (Upload new to replace)</label>
            <?php if (!empty($settings['favicon'])): ?>
                <p style="margin-bottom:5px;"><small>Current: <?php echo e($settings['favicon']); ?></small></p>
            <?php endif; ?>
            <input type="file" id="favicon" name="favicon" accept="image/jpeg,image/png,image/webp">
        </div>
    </div>

    <!-- Hero Section -->
    <div class="admin-card">
        <h3 class="section-title">Hero Section</h3>
        <div class="form-group">
            <label for="hero_heading">Hero Heading</label>
            <input type="text" id="hero_heading" name="hero_heading" value="<?php echo e($settings['hero_heading'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="hero_subheading">Hero Subheading</label>
            <textarea id="hero_subheading" name="hero_subheading" rows="3"><?php echo e($settings['hero_subheading'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="hero_image">Hero Background Image</label>
            <?php if (!empty($settings['hero_image'])): ?>
                <p style="margin-bottom:5px;"><small>Current: <?php echo e($settings['hero_image']); ?></small></p>
            <?php endif; ?>
            <input type="file" id="hero_image" name="hero_image" accept="image/jpeg,image/png,image/webp">
        </div>
    </div>

    <!-- About Section -->
    <div class="admin-card">
        <h3 class="section-title">About Section</h3>
        <div class="form-group">
            <label for="about_content">About Content</label>
            <textarea id="about_content" name="about_content" rows="6"><?php echo e($settings['about_content'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Colors Section -->
    <div class="admin-card">
        <h3 class="section-title">Color Settings</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="primary_color">Primary Color</label>
                <input type="color" id="primary_color" name="primary_color" value="<?php echo e($settings['primary_color'] ?? '#e63946'); ?>">
            </div>
            <div class="form-group">
                <label for="secondary_color">Secondary Color</label>
                <input type="color" id="secondary_color" name="secondary_color" value="<?php echo e($settings['secondary_color'] ?? '#1d1d1d'); ?>">
            </div>
            <div class="form-group">
                <label for="button_color">Button Color</label>
                <input type="color" id="button_color" name="button_color" value="<?php echo e($settings['button_color'] ?? '#e63946'); ?>">
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="admin-card">
        <h3 class="section-title">Contact Information</h3>
        <div class="form-group">
            <label for="whatsapp_number">WhatsApp Number (with country code, no +)</label>
            <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo e($settings['whatsapp_number'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo e($settings['phone_number'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo e($settings['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3"><?php echo e($settings['address'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="google_map_embed">Google Map Embed Code</label>
            <textarea id="google_map_embed" name="google_map_embed" rows="4"><?php echo e($settings['google_map_embed'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Business Hours -->
    <div class="admin-card">
        <h3 class="section-title">Business Hours</h3>
        <div class="form-group">
            <label for="business_hours">Business Hours</label>
            <textarea id="business_hours" name="business_hours" rows="3"><?php echo e($settings['business_hours'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Social Media -->
    <div class="admin-card">
        <h3 class="section-title">Social Media Links</h3>
        <div class="form-group">
            <label for="instagram_link">Instagram URL</label>
            <input type="url" id="instagram_link" name="instagram_link" value="<?php echo e($settings['instagram_link'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="facebook_link">Facebook URL</label>
            <input type="url" id="facebook_link" name="facebook_link" value="<?php echo e($settings['facebook_link'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="youtube_link">YouTube URL</label>
            <input type="url" id="youtube_link" name="youtube_link" value="<?php echo e($settings['youtube_link'] ?? ''); ?>">
        </div>
    </div>

    <!-- Footer -->
    <div class="admin-card">
        <h3 class="section-title">Footer</h3>
        <div class="form-group">
            <label for="footer_text">Footer Text</label>
            <textarea id="footer_text" name="footer_text" rows="4"><?php echo e($settings['footer_text'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Analytics -->
    <div class="admin-card">
        <h3 class="section-title">Analytics & Tracking</h3>
        <div class="form-group">
            <label for="google_analytics">Google Analytics Code</label>
            <textarea id="google_analytics" name="google_analytics" rows="4"><?php echo e($settings['google_analytics'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="search_console">Google Search Console Verification</label>
            <input type="text" id="search_console" name="search_console" value="<?php echo e($settings['search_console'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="facebook_pixel">Facebook Pixel Code</label>
            <textarea id="facebook_pixel" name="facebook_pixel" rows="4"><?php echo e($settings['facebook_pixel'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Default SEO -->
    <div class="admin-card">
        <h3 class="section-title">Default SEO Settings</h3>
        <div class="form-group">
            <label for="default_meta_title">Default Meta Title</label>
            <input type="text" id="default_meta_title" name="default_meta_title" value="<?php echo e($settings['default_meta_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="default_meta_description">Default Meta Description</label>
            <textarea id="default_meta_description" name="default_meta_description" rows="3"><?php echo e($settings['default_meta_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="default_meta_keywords">Default Meta Keywords</label>
            <input type="text" id="default_meta_keywords" name="default_meta_keywords" value="<?php echo e($settings['default_meta_keywords'] ?? ''); ?>">
        </div>
    </div>

    <!-- Services -->
    <div class="admin-card">
        <h3 class="section-title">Services (JSON)</h3>
        <div class="form-group">
            <label for="services">Services JSON (array of objects with title, description, icon)</label>
            <textarea id="services" name="services" rows="8"><?php echo e($settings['services'] ?? '[]'); ?></textarea>
        </div>
    </div>

    <!-- FAQs -->
    <div class="admin-card">
        <h3 class="section-title">FAQs (JSON)</h3>
        <div class="form-group">
            <label for="faqs">FAQs JSON (array of objects with question, answer)</label>
            <textarea id="faqs" name="faqs" rows="8"><?php echo e($settings['faqs'] ?? '[]'); ?></textarea>
        </div>
    </div>

    <div class="admin-card">
        <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 1rem;">
            <i class="fas fa-save"></i> Save All Settings
        </button>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>
