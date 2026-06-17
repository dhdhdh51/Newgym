<?php
/**
 * Admin SEO Manager
 * Manage SEO settings for all pages
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'SEO Manager';
$success = '';
$error = '';
$editPage = $_GET['page_key'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $pageKey = sanitizeInput($_POST['page_key'] ?? '');
        $data = [
            ':meta_title' => $_POST['meta_title'] ?? '',
            ':meta_description' => $_POST['meta_description'] ?? '',
            ':meta_keywords' => $_POST['meta_keywords'] ?? '',
            ':slug' => $_POST['slug'] ?? '',
            ':canonical_url' => $_POST['canonical_url'] ?? '',
            ':robots_meta' => $_POST['robots_meta'] ?? 'index, follow',
            ':og_title' => $_POST['og_title'] ?? '',
            ':og_description' => $_POST['og_description'] ?? '',
            ':og_image' => $_POST['og_image'] ?? '',
            ':twitter_title' => $_POST['twitter_title'] ?? '',
            ':twitter_description' => $_POST['twitter_description'] ?? '',
            ':twitter_image' => $_POST['twitter_image'] ?? '',
            ':schema_json' => $_POST['schema_json'] ?? '',
            ':focus_keyword' => $_POST['focus_keyword'] ?? '',
            ':custom_header_scripts' => $_POST['custom_header_scripts'] ?? '',
            ':custom_footer_scripts' => $_POST['custom_footer_scripts'] ?? '',
            ':page_key' => $pageKey
        ];

        try {
            $stmt = $pdo->prepare("UPDATE seo_settings SET 
                meta_title = :meta_title, meta_description = :meta_description, meta_keywords = :meta_keywords,
                slug = :slug, canonical_url = :canonical_url, robots_meta = :robots_meta,
                og_title = :og_title, og_description = :og_description, og_image = :og_image,
                twitter_title = :twitter_title, twitter_description = :twitter_description, twitter_image = :twitter_image,
                schema_json = :schema_json, focus_keyword = :focus_keyword,
                custom_header_scripts = :custom_header_scripts, custom_footer_scripts = :custom_footer_scripts
                WHERE page_key = :page_key");
            $stmt->execute($data);
            $success = 'SEO settings updated successfully!';
            $editPage = $pageKey;
        } catch (PDOException $e) {
            $error = 'Failed to update SEO settings.';
        }
    }
}

// Get all pages
try {
    $pages = $pdo->query("SELECT * FROM seo_settings ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $pages = [];
}

// Get current page for editing
$currentSeo = [];
if ($editPage) {
    foreach ($pages as $page) {
        if ($page['page_key'] === $editPage) {
            $currentSeo = $page;
            break;
        }
    }
}

include 'includes/admin-header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if ($editPage && $currentSeo): ?>
<!-- Edit SEO Form -->
<div class="admin-card">
    <h3 class="section-title">Edit SEO - <?php echo e($currentSeo['page_name']); ?></h3>
    <a href="seo-manager.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to List</a>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" name="page_key" value="<?php echo e($currentSeo['page_key']); ?>">

        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" value="<?php echo e($currentSeo['meta_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" rows="3"><?php echo e($currentSeo['meta_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="meta_keywords">Meta Keywords</label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo e($currentSeo['meta_keywords'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo e($currentSeo['slug'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="canonical_url">Canonical URL</label>
            <input type="text" id="canonical_url" name="canonical_url" value="<?php echo e($currentSeo['canonical_url'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="robots_meta">Robots Meta</label>
            <input type="text" id="robots_meta" name="robots_meta" value="<?php echo e($currentSeo['robots_meta'] ?? 'index, follow'); ?>">
        </div>
        <div class="form-group">
            <label for="og_title">OG Title</label>
            <input type="text" id="og_title" name="og_title" value="<?php echo e($currentSeo['og_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="og_description">OG Description</label>
            <textarea id="og_description" name="og_description" rows="3"><?php echo e($currentSeo['og_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="og_image">OG Image URL</label>
            <input type="text" id="og_image" name="og_image" value="<?php echo e($currentSeo['og_image'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="twitter_title">Twitter Title</label>
            <input type="text" id="twitter_title" name="twitter_title" value="<?php echo e($currentSeo['twitter_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="twitter_description">Twitter Description</label>
            <textarea id="twitter_description" name="twitter_description" rows="3"><?php echo e($currentSeo['twitter_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="twitter_image">Twitter Image URL</label>
            <input type="text" id="twitter_image" name="twitter_image" value="<?php echo e($currentSeo['twitter_image'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="schema_json">Schema JSON-LD</label>
            <textarea id="schema_json" name="schema_json" rows="6"><?php echo e($currentSeo['schema_json'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="focus_keyword">Focus Keyword</label>
            <input type="text" id="focus_keyword" name="focus_keyword" value="<?php echo e($currentSeo['focus_keyword'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="custom_header_scripts">Custom Header Scripts</label>
            <textarea id="custom_header_scripts" name="custom_header_scripts" rows="4"><?php echo e($currentSeo['custom_header_scripts'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="custom_footer_scripts">Custom Footer Scripts</label>
            <textarea id="custom_footer_scripts" name="custom_footer_scripts" rows="4"><?php echo e($currentSeo['custom_footer_scripts'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save SEO Settings</button>
    </form>
</div>

<?php else: ?>
<!-- List all pages -->
<div class="admin-card">
    <h3 class="section-title">All Pages SEO</h3>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Meta Title</th>
                    <th>Focus Keyword</th>
                    <th>Robots</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                <tr>
                    <td><strong><?php echo e($page['page_name']); ?></strong></td>
                    <td><?php echo e(truncateText($page['meta_title'] ?? '', 50)); ?></td>
                    <td><?php echo e($page['focus_keyword'] ?? '-'); ?></td>
                    <td><?php echo e($page['robots_meta'] ?? 'index, follow'); ?></td>
                    <td>
                        <a href="seo-manager.php?page_key=<?php echo e($page['page_key']); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
