<?php
/**
 * Admin Add Blog Post
 * Form to create a new blog post
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Add Blog Post';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title = sanitizeInput($_POST['title'] ?? '');
        $slug = sanitizeInput($_POST['slug'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? '');
        $author = sanitizeInput($_POST['author'] ?? 'Admin');
        $content = $_POST['content'] ?? '';
        $metaTitle = sanitizeInput($_POST['meta_title'] ?? '');
        $metaDescription = $_POST['meta_description'] ?? '';
        $metaKeywords = sanitizeInput($_POST['meta_keywords'] ?? '');
        $canonicalUrl = sanitizeInput($_POST['canonical_url'] ?? '');
        $schemaJson = $_POST['schema_json'] ?? '';
        $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

        // Auto-generate slug if empty
        if (empty($slug) && !empty($title)) {
            $slug = generateSlug($title);
        }

        // Handle featured image
        $featuredImage = sanitizeInput($_POST['featured_image_url'] ?? '');
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['featured_image'], 'uploads/');
            if ($uploadedPath) {
                $featuredImage = $uploadedPath;
            }
        }

        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        if (empty($title) || empty($content)) {
            $error = 'Title and content are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, category, author, featured_image, content, meta_title, meta_description, meta_keywords, canonical_url, schema_json, status, published_at) VALUES (:title, :slug, :category, :author, :image, :content, :meta_title, :meta_desc, :meta_keys, :canonical, :schema, :status, :published)");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':category' => $category,
                    ':author' => $author,
                    ':image' => $featuredImage,
                    ':content' => $content,
                    ':meta_title' => $metaTitle,
                    ':meta_desc' => $metaDescription,
                    ':meta_keys' => $metaKeywords,
                    ':canonical' => $canonicalUrl,
                    ':schema' => $schemaJson,
                    ':status' => $status,
                    ':published' => $publishedAt
                ]);
                $success = 'Blog post created successfully!';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'A post with this slug already exists. Please use a different slug.';
                } else {
                    $error = 'Failed to create blog post.';
                }
            }
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

<div class="admin-card">
    <h3 class="section-title">Add New Blog Post</h3>
    <a href="blog-manager.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Posts</a>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" required value="<?php echo e($_POST['title'] ?? ''); ?>" onkeyup="document.getElementById('slug').value = generateSlug(this.value);">
        </div>
        <div class="form-group">
            <label for="slug">Slug (auto-generated from title)</label>
            <input type="text" id="slug" name="slug" value="<?php echo e($_POST['slug'] ?? ''); ?>" placeholder="auto-generated-from-title">
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" placeholder="e.g., Fitness, Nutrition, Tips" value="<?php echo e($_POST['category'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?php echo e($_POST['author'] ?? 'Admin'); ?>">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="featured_image">Featured Image (Upload)</label>
            <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="featured_image_url">Or Featured Image URL</label>
            <input type="text" id="featured_image_url" name="featured_image_url" placeholder="https://..." value="<?php echo e($_POST['featured_image_url'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="content">Content *</label>
            <textarea id="content" name="content" rows="12" required><?php echo e($_POST['content'] ?? ''); ?></textarea>
        </div>

        <h4 style="margin: 20px 0 15px; color:#333;">SEO Settings</h4>
        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" value="<?php echo e($_POST['meta_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" rows="3"><?php echo e($_POST['meta_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="meta_keywords">Meta Keywords</label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo e($_POST['meta_keywords'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="canonical_url">Canonical URL</label>
            <input type="text" id="canonical_url" name="canonical_url" value="<?php echo e($_POST['canonical_url'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="schema_json">Schema JSON-LD</label>
            <textarea id="schema_json" name="schema_json" rows="4"><?php echo e($_POST['schema_json'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Post</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
