<?php
/**
 * Admin Trainers List
 * List all trainers with actions
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Trainers';
$success = '';
$error = '';

// Handle toggle active
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("UPDATE trainers SET is_active = NOT is_active WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Trainer status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update trainer status.';
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
            $stmt = $pdo->prepare("DELETE FROM trainers WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Trainer deleted successfully.';
        } catch (PDOException $e) {
            $error = 'Failed to delete trainer.';
        }
    }
}

// Get all trainers
try {
    $trainers = $pdo->query("SELECT * FROM trainers ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $trainers = [];
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

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="section-title" style="margin-bottom:0; border-bottom:none;">All Trainers</h3>
        <a href="trainer-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Trainer</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialty</th>
                    <th>Experience</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trainers)): ?>
                    <tr><td colspan="6" style="text-align:center;">No trainers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($trainers as $trainer): ?>
                    <tr>
                        <td><?php echo (int)$trainer['id']; ?></td>
                        <td><strong><?php echo e($trainer['name']); ?></strong></td>
                        <td><?php echo e($trainer['specialty']); ?></td>
                        <td><?php echo e($trainer['experience']); ?></td>
                        <td><?php echo $trainer['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                        <td>
                            <a href="trainer-edit.php?id=<?php echo (int)$trainer['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="trainers.php?toggle=<?php echo (int)$trainer['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-on"></i></a>
                            <a href="trainers.php?delete=<?php echo (int)$trainer['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this trainer?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
