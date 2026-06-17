<?php
/**
 * Admin Classes List
 * List all classes with actions
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Classes';
$success = '';
$error = '';

// Handle toggle active
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("UPDATE classes SET is_active = NOT is_active WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Class status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update class status.';
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
            $stmt = $pdo->prepare("DELETE FROM classes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Class deleted successfully.';
        } catch (PDOException $e) {
            $error = 'Failed to delete class.';
        }
    }
}

// Get all classes
try {
    $classes = $pdo->query("SELECT * FROM classes ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $classes = [];
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
        <h3 class="section-title" style="margin-bottom:0; border-bottom:none;">All Classes</h3>
        <a href="class-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Class</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Trainer</th>
                    <th>Time</th>
                    <th>Days</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($classes)): ?>
                    <tr><td colspan="7" style="text-align:center;">No classes found.</td></tr>
                <?php else: ?>
                    <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?php echo (int)$class['id']; ?></td>
                        <td><strong><?php echo e($class['class_name']); ?></strong></td>
                        <td><?php echo e($class['trainer_name']); ?></td>
                        <td><?php echo e($class['class_time']); ?></td>
                        <td><?php echo e($class['class_days']); ?></td>
                        <td><?php echo $class['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                        <td>
                            <a href="class-edit.php?id=<?php echo (int)$class['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="classes.php?toggle=<?php echo (int)$class['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-on"></i></a>
                            <a href="classes.php?delete=<?php echo (int)$class['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this class?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
