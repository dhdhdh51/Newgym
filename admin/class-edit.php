<?php
/**
 * Admin Edit Class
 * Edit an existing class
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Edit Class';
$success = '';
$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('classes.php');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $class = $stmt->fetch();
    if (!$class) {
        redirect('classes.php');
    }
} catch (PDOException $e) {
    redirect('classes.php');
}

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

        // Handle image
        $image = $class['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['image'], 'uploads/');
            if ($uploadedPath) {
                $image = $uploadedPath;
            }
        } elseif (!empty($_POST['image_url'])) {
            $image = sanitizeInput($_POST['image_url']);
        }

        if (empty($className) || empty($trainerName)) {
            $error = 'Class name and trainer name are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE classes SET class_name = :name, trainer_name = :trainer, class_time = :time, class_days = :days, duration = :duration, description = :description, image = :image, is_active = :active WHERE id = :id");
                $stmt->execute([
                    ':name' => $className,
                    ':trainer' => $trainerName,
                    ':time' => $classTime,
                    ':days' => $classDays,
                    ':duration' => $duration,
                    ':description' => $description,
                    ':image' => $image,
                    ':active' => $isActive,
                    ':id' => $id
                ]);
                $success = 'Class updated successfully!';
                $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $class = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Failed to update class.';
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
    <h3 class="section-title">Edit Class: <?php echo e($class['class_name']); ?></h3>
    <a href="classes.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Classes</a>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="class_name">Class Name *</label>
            <input type="text" id="class_name" name="class_name" required value="<?php echo e($class['class_name']); ?>">
        </div>
        <div class="form-group">
            <label for="trainer_name">Trainer Name *</label>
            <input type="text" id="trainer_name" name="trainer_name" required value="<?php echo e($class['trainer_name']); ?>">
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="class_time">Class Time</label>
                <input type="text" id="class_time" name="class_time" value="<?php echo e($class['class_time']); ?>">
            </div>
            <div class="form-group">
                <label for="class_days">Class Days</label>
                <input type="text" id="class_days" name="class_days" value="<?php echo e($class['class_days']); ?>">
            </div>
            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration" value="<?php echo e($class['duration']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo e($class['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image (Upload new to replace)</label>
            <?php if (!empty($class['image'])): ?>
                <p style="margin-bottom:5px;"><small>Current: <?php echo e($class['image']); ?></small></p>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="image_url">Or Image URL</label>
            <input type="text" id="image_url" name="image_url" value="<?php echo e($class['image'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo $class['is_active'] ? 'checked' : ''; ?>> Active</label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Class</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
