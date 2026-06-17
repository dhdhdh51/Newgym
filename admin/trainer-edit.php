<?php
/**
 * Admin Edit Trainer
 * Edit an existing trainer
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Edit Trainer';
$success = '';
$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('trainers.php');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM trainers WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $trainer = $stmt->fetch();
    if (!$trainer) {
        redirect('trainers.php');
    }
} catch (PDOException $e) {
    redirect('trainers.php');
}

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
        $photo = $trainer['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = uploadImage($_FILES['photo'], 'uploads/');
            if ($uploadedPath) {
                $photo = $uploadedPath;
            }
        } elseif (!empty($_POST['photo_url'])) {
            $photo = sanitizeInput($_POST['photo_url']);
        }

        if (empty($name) || empty($specialty)) {
            $error = 'Name and specialty are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE trainers SET name = :name, photo = :photo, specialty = :specialty, experience = :experience, bio = :bio, instagram_link = :instagram, is_active = :active WHERE id = :id");
                $stmt->execute([
                    ':name' => $name,
                    ':photo' => $photo,
                    ':specialty' => $specialty,
                    ':experience' => $experience,
                    ':bio' => $bio,
                    ':instagram' => $instagramLink,
                    ':active' => $isActive,
                    ':id' => $id
                ]);
                $success = 'Trainer updated successfully!';
                $stmt = $pdo->prepare("SELECT * FROM trainers WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $trainer = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Failed to update trainer.';
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
    <h3 class="section-title">Edit Trainer: <?php echo e($trainer['name']); ?></h3>
    <a href="trainers.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Trainers</a>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required value="<?php echo e($trainer['name']); ?>">
        </div>
        <div class="form-group">
            <label for="photo">Photo (Upload new to replace)</label>
            <?php if (!empty($trainer['photo'])): ?>
                <p style="margin-bottom:5px;"><small>Current: <?php echo e($trainer['photo']); ?></small></p>
            <?php endif; ?>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="photo_url">Or Photo URL</label>
            <input type="text" id="photo_url" name="photo_url" placeholder="https://..." value="<?php echo e($trainer['photo'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="specialty">Specialty *</label>
            <input type="text" id="specialty" name="specialty" required value="<?php echo e($trainer['specialty']); ?>">
        </div>
        <div class="form-group">
            <label for="experience">Experience</label>
            <input type="text" id="experience" name="experience" value="<?php echo e($trainer['experience']); ?>">
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"><?php echo e($trainer['bio'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="instagram_link">Instagram Link</label>
            <input type="text" id="instagram_link" name="instagram_link" value="<?php echo e($trainer['instagram_link'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo $trainer['is_active'] ? 'checked' : ''; ?>> Active</label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Trainer</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
