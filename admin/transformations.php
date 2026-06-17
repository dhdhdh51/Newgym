<?php
/**
 * Admin Transformations
 * Manage member transformations (before/after)
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Transformations';
$success = '';
$error = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $memberName = sanitizeInput($_POST['member_name'] ?? '');
        $duration = sanitizeInput($_POST['duration'] ?? '');
        $description = $_POST['description'] ?? '';

        $beforeImage = sanitizeInput($_POST['before_image_url'] ?? '');
        if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === UPLOAD_ERR_OK) {
            $path = uploadImage($_FILES['before_image'], 'uploads/');
            if ($path) $beforeImage = $path;
        }

        $afterImage = sanitizeInput($_POST['after_image_url'] ?? '');
        if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === UPLOAD_ERR_OK) {
            $path = uploadImage($_FILES['after_image'], 'uploads/');
            if ($path) $afterImage = $path;
        }

        if (empty($memberName) || empty($beforeImage) || empty($afterImage)) {
            $error = 'Member name and both images are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO transformations (member_name, before_image, after_image, duration, description) VALUES (:name, :before, :after, :duration, :desc)");
                $stmt->execute([':name' => $memberName, ':before' => $beforeImage, ':after' => $afterImage, ':duration' => $duration, ':desc' => $description]);
                $success = 'Transformation added successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to add transformation.';
            }
        }
    }
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $editId = (int)($_POST['edit_id'] ?? 0);
        $memberName = sanitizeInput($_POST['member_name'] ?? '');
        $duration = sanitizeInput($_POST['duration'] ?? '');
        $description = $_POST['description'] ?? '';

        // Get current record
        $stmt = $pdo->prepare("SELECT * FROM transformations WHERE id = :id");
        $stmt->execute([':id' => $editId]);
        $current = $stmt->fetch();

        $beforeImage = $current['before_image'] ?? '';
        if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === UPLOAD_ERR_OK) {
            $path = uploadImage($_FILES['before_image'], 'uploads/');
            if ($path) $beforeImage = $path;
        } elseif (!empty($_POST['before_image_url'])) {
            $beforeImage = sanitizeInput($_POST['before_image_url']);
        }

        $afterImage = $current['after_image'] ?? '';
        if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === UPLOAD_ERR_OK) {
            $path = uploadImage($_FILES['after_image'], 'uploads/');
            if ($path) $afterImage = $path;
        } elseif (!empty($_POST['after_image_url'])) {
            $afterImage = sanitizeInput($_POST['after_image_url']);
        }

        if (empty($memberName) || $editId <= 0) {
            $error = 'Member name is required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE transformations SET member_name = :name, before_image = :before, after_image = :after, duration = :duration, description = :desc WHERE id = :id");
                $stmt->execute([':name' => $memberName, ':before' => $beforeImage, ':after' => $afterImage, ':duration' => $duration, ':desc' => $description, ':id' => $editId]);
                $success = 'Transformation updated!';
            } catch (PDOException $e) {
                $error = 'Failed to update transformation.';
            }
        }
    }
}

// Handle toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (verifyCsrfToken($_GET['token'] ?? '')) {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("UPDATE transformations SET is_active = NOT is_active WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update status.';
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCsrfToken($_GET['token'] ?? '')) {
        $id = (int)$_GET['delete'];
        try {
            $stmt = $pdo->prepare("DELETE FROM transformations WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Transformation deleted.';
        } catch (PDOException $e) {
            $error = 'Failed to delete.';
        }
    }
}

// Get all
try {
    $items = $pdo->query("SELECT * FROM transformations ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $items = [];
}

$token = generateCsrfToken();
$editItem = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    foreach ($items as $item) {
        if ((int)$item['id'] === (int)$_GET['edit']) {
            $editItem = $item;
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

<!-- Add/Edit Form -->
<div class="admin-card">
    <h3 class="section-title"><?php echo $editItem ? 'Edit Transformation' : 'Add New Transformation'; ?></h3>
    <?php if ($editItem): ?>
        <a href="transformations.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-times"></i> Cancel Edit</a>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <input type="hidden" name="action" value="<?php echo $editItem ? 'edit' : 'add'; ?>">
        <?php if ($editItem): ?>
            <input type="hidden" name="edit_id" value="<?php echo (int)$editItem['id']; ?>">
        <?php endif; ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;">
            <div class="form-group">
                <label for="member_name">Member Name *</label>
                <input type="text" id="member_name" name="member_name" required value="<?php echo e($editItem['member_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration" placeholder="e.g., 3 months" value="<?php echo e($editItem['duration'] ?? ''); ?>">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                <div class="form-group">
                    <label>Before Image (Upload)</label>
                    <input type="file" name="before_image" accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="form-group">
                    <label>Or Before Image URL</label>
                    <input type="text" name="before_image_url" value="<?php echo e($editItem['before_image'] ?? ''); ?>">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label>After Image (Upload)</label>
                    <input type="file" name="after_image" accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="form-group">
                    <label>Or After Image URL</label>
                    <input type="text" name="after_image_url" value="<?php echo e($editItem['after_image'] ?? ''); ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?php echo e($editItem['description'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editItem ? 'Update' : 'Add'; ?> Transformation</button>
    </form>
</div>

<!-- List -->
<div class="admin-card">
    <h3 class="section-title">All Transformations (<?php echo count($items); ?>)</h3>
    <?php if (empty($items)): ?>
        <p>No transformations yet.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Duration</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo (int)$item['id']; ?></td>
                        <td><strong><?php echo e($item['member_name']); ?></strong></td>
                        <td><?php echo e($item['duration']); ?></td>
                        <td><?php echo $item['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                        <td>
                            <a href="transformations.php?edit=<?php echo (int)$item['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="transformations.php?toggle=<?php echo (int)$item['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-on"></i></a>
                            <a href="transformations.php?delete=<?php echo (int)$item['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this transformation?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin-footer.php'; ?>
