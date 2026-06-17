<?php
/**
 * Admin Add Class
 * Form to add a new class
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Add Class';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $className = sanitizeInput($_POST['class_name'] ?? '');
        $trainerName = sanitizeInput($_POST['trainer_name'] ?? '');
        $classTime = sanitizeInput($_POST['class_time'] ?? '');
        $classDays = sanitizeInput($_POST['class_days'] ?? '');
        $duration = sanitizeInput($_POST['duration'] ?? '');
        $description = $_POST['description'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Handle image upload or URL
        $image = sanitizeInput($_POST['image_url'] ?? '');
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['image'], 'uploads/');
            if ($uploadedPath) {
                $image = $uploadedPath;
            }
        }

        if (empty($className) || empty($trainerName)) {
            $error = 'Class name and trainer name are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO classes (class_name, trainer_name, class_time, class_days, duration, description, image, is_active) VALUES (:name, :trainer, :time, :days, :duration, :description, :image, :active)");
                $stmt->execute([
                    ':name' => $className,
                    ':trainer' => $trainerName,
                    ':time' => $classTime,
                    ':days' => $classDays,
                    ':duration' => $duration,
                    ':description' => $description,
                    ':image' => $image,
                    ':active' => $isActive
                ]);
                $success = 'Class added successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to add class.';
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
    <h3 class="section-title">Add New Class</h3>
    <a href="classes.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Classes</a>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="class_name">Class Name *</label>
            <input type="text" id="class_name" name="class_name" required value="<?php echo e($_POST['class_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="trainer_name">Trainer Name *</label>
            <input type="text" id="trainer_name" name="trainer_name" required value="<?php echo e($_POST['trainer_name'] ?? ''); ?>">
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="class_time">Class Time</label>
                <input type="text" id="class_time" name="class_time" placeholder="e.g., 6:00 AM - 7:00 AM" value="<?php echo e($_POST['class_time'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="class_days">Class Days</label>
                <input type="text" id="class_days" name="class_days" placeholder="e.g., Monday, Wednesday, Friday" value="<?php echo e($_POST['class_days'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration" placeholder="e.g., 60 minutes" value="<?php echo e($_POST['duration'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo e($_POST['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image (Upload)</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="image_url">Or Image URL</label>
            <input type="text" id="image_url" name="image_url" placeholder="https://..." value="<?php echo e($_POST['image_url'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo (!isset($_POST['is_active']) && $_SERVER['REQUEST_METHOD'] !== 'POST') ? 'checked' : (isset($_POST['is_active']) ? 'checked' : ''); ?>> Active</label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Class</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
