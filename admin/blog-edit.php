<?php
/**
 * Admin Edit Blog Post
 * Edit an existing blog post
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Edit Blog Post';
$success = '';
$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('blog-manager.php');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch();
    if (!$post) {
        redirect('blog-manager.php');
    }
} catch (PDOException $e) {
    redirect('blog-manager.php');
}

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

        if (empty($slug) && !empty($title)) {
            $slug = generateSlug($title);
        }

        // Handle featured image
        $featuredImage = $post['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['featured_image'], 'uploads/');
            if ($uploadedPath) {
                $featuredImage = $uploadedPath;
            }
        } elseif (!empty($_POST['featured_image_url'])) {
            $featuredImage = sanitizeInput($_POST['featured_image_url']);
        }

        // Set published_at if publishing for first time
        $publishedAt = $post['published_at'];
        if ($status === 'published' && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        } elseif ($status === 'draft') {
            $publishedAt = null;
        }

        if (empty($title) || empty($content)) {
            $error = 'Title and content are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE blog_posts SET title = :title, slug = :slug, category = :category, author = :author, featured_image = :image, content = :content, meta_title = :meta_title, meta_description = :meta_desc, meta_keywords = :meta_keys, canonical_url = :canonical, schema_json = :schema, status = :status, published_at = :published WHERE id = :id");
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
                    ':published' => $publishedAt,
                    ':id' => $id
                ]);
                $success = 'Blog post updated successfully!';
                $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $post = $stmt->fetch();
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'A post with this slug already exists.';
                } else {
                    $error = 'Failed to update blog post.';
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
    <h3 class="section-title">Edit Post: <?php echo e($post['title']); ?></h3>
    <a href="blog-manager.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Posts</a>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" required value="<?php echo e($post['title']); ?>" onkeyup="document.getElementById('slug').value = generateSlug(this.value);">
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo e($post['slug']); ?>">
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" value="<?php echo e($post['category'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?php echo e($post['author'] ?? 'Admin'); ?>">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="featured_image">Featured Image (Upload new to replace)</label>
            <?php if (!empty($post['featured_image'])): ?>
                <p style="margin-bottom:5px;"><small>Current: <?php echo e($post['featured_image']); ?></small></p>
            <?php endif; ?>
            <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="featured_image_url">Or Featured Image URL</label>
            <input type="text" id="featured_image_url" name="featured_image_url" value="<?php echo e($post['featured_image'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="content">Content *</label>
            <textarea id="content" name="content" rows="12" required><?php echo e($post['content']); ?></textarea>
        </div>

        <h4 style="margin: 20px 0 15px; color:#333;">SEO Settings</h4>
        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" value="<?php echo e($post['meta_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" rows="3"><?php echo e($post['meta_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="meta_keywords">Meta Keywords</label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo e($post['meta_keywords'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="canonical_url">Canonical URL</label>
            <input type="text" id="canonical_url" name="canonical_url" value="<?php echo e($post['canonical_url'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="schema_json">Schema JSON-LD</label>
            <textarea id="schema_json" name="schema_json" rows="4"><?php echo e($post['schema_json'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Post</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
