<?php
/**
 * Admin Add Trainer
 * Form to add a new trainer
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Add Trainer';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $specialty = sanitizeInput($_POST['specialty'] ?? '');
        $experience = sanitizeInput($_POST['experience'] ?? '');
        $bio = $_POST['bio'] ?? '';
        $instagramLink = sanitizeInput($_POST['instagram_link'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Handle photo upload or URL
        $photo = sanitizeInput($_POST['photo_url'] ?? '');
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['photo'], 'uploads/');
            if ($uploadedPath) {
                $photo = $uploadedPath;
            }
        }

        if (empty($name) || empty($specialty)) {
            $error = 'Name and specialty are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO trainers (name, photo, specialty, experience, bio, instagram_link, is_active) VALUES (:name, :photo, :specialty, :experience, :bio, :instagram, :active)");
                $stmt->execute([
                    ':name' => $name,
                    ':photo' => $photo,
                    ':specialty' => $specialty,
                    ':experience' => $experience,
                    ':bio' => $bio,
                    ':instagram' => $instagramLink,
                    ':active' => $isActive
                ]);
                $success = 'Trainer added successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to add trainer.';
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
    <h3 class="section-title">Add New Trainer</h3>
    <a href="trainers.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Trainers</a>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="photo">Photo (Upload)</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="photo_url">Or Photo URL</label>
            <input type="text" id="photo_url" name="photo_url" placeholder="https://..." value="<?php echo e($_POST['photo_url'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="specialty">Specialty *</label>
            <input type="text" id="specialty" name="specialty" required value="<?php echo e($_POST['specialty'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="experience">Experience</label>
            <input type="text" id="experience" name="experience" placeholder="e.g., 5+ years" value="<?php echo e($_POST['experience'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"><?php echo e($_POST['bio'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="instagram_link">Instagram Link</label>
            <input type="text" id="instagram_link" name="instagram_link" value="<?php echo e($_POST['instagram_link'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo (!isset($_POST['is_active']) && $_SERVER['REQUEST_METHOD'] !== 'POST') ? 'checked' : (isset($_POST['is_active']) ? 'checked' : ''); ?>> Active</label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Trainer</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
