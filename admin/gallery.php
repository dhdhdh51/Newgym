<?php
/**
 * Admin Gallery
 * Manage gallery images
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Gallery';
$success = '';
$error = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title = sanitizeInput($_POST['title'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? 'general');

        // Handle image upload or URL
        $image = sanitizeInput($_POST['image_url'] ?? '');
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['image'], 'uploads/');
            if ($uploadedPath) {
                $image = $uploadedPath;
            }
        }

        if (empty($title) || empty($image)) {
            $error = 'Title and image are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, image, category) VALUES (:title, :image, :category)");
                $stmt->execute([':title' => $title, ':image' => $image, ':category' => $category]);
                $success = 'Gallery item added successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to add gallery item.';
            }
        }
    }
}

// Handle toggle active
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("UPDATE gallery SET is_active = NOT is_active WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Gallery item status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update gallery item.';
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['delete'];
        try {
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Gallery item deleted.';
        } catch (PDOException $e) {
            $error = 'Failed to delete gallery item.';
        }
    }
}

// Get all gallery items
try {
    $items = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $items = [];
}

$token = generateCsrfToken();
include 'includes/admin-header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo e($error); ?></div>
<?php endif; ?>

<!-- Add New Image -->
<div class="admin-card">
    <h3 class="section-title">Add New Image</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <input type="hidden" name="action" value="add">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" placeholder="e.g., gym, equipment, classes" value="general">
            </div>
        </div>
        <div class="form-group">
            <label for="image">Image (Upload)</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="image_url">Or Image URL</label>
            <input type="text" id="image_url" name="image_url" placeholder="https://...">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Image</button>
    </form>
</div>

<!-- Gallery Grid -->
<div class="admin-card">
    <h3 class="section-title">Gallery Images (<?php echo count($items); ?>)</h3>
    <?php if (empty($items)): ?>
        <p>No gallery items yet.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($items as $item): ?>
            <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; background: #f9f9f9;">
                <div style="height: 180px; background: #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <img src="../<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-image\' style=\'font-size:3rem;color:#999;\'></i>';">
                </div>
                <div style="padding: 15px;">
                    <strong><?php echo e($item['title']); ?></strong>
                    <p style="margin:5px 0; font-size:0.85rem; color:#666;">Category: <?php echo e($item['category']); ?></p>
                    <p style="margin:5px 0;">
                        <?php echo $item['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>
                    </p>
                    <div style="margin-top: 10px;">
                        <a href="gallery.php?toggle=<?php echo (int)$item['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-on"></i></a>
                        <a href="gallery.php?delete=<?php echo (int)$item['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this image?')"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin-footer.php'; ?>
