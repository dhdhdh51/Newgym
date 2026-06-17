<?php
/**
 * Admin Plans List
 * List all membership plans with actions
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Membership Plans';
$success = '';
$error = '';

// Handle toggle active
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("UPDATE membership_plans SET is_active = NOT is_active WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Plan status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update plan status.';
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
            $stmt = $pdo->prepare("DELETE FROM membership_plans WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Plan deleted successfully.';
        } catch (PDOException $e) {
            $error = 'Failed to delete plan.';
        }
    }
}

// Get all plans
try {
    $plans = $pdo->query("SELECT * FROM membership_plans ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $plans = [];
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
        <h3 class="section-title" style="margin-bottom:0; border-bottom:none;">All Plans</h3>
        <a href="plan-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Plan</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plan Name</th>
                    <th>Monthly Price</th>
                    <th>Highlighted</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plans)): ?>
                    <tr><td colspan="6" style="text-align:center;">No plans found.</td></tr>
                <?php else: ?>
                    <?php foreach ($plans as $plan): ?>
                    <tr>
                        <td><?php echo (int)$plan['id']; ?></td>
                        <td><strong><?php echo e($plan['plan_name']); ?></strong></td>
                        <td><?php echo number_format((float)$plan['monthly_price'], 2); ?></td>
                        <td><?php echo $plan['is_highlighted'] ? '<span class="badge badge-info">Yes</span>' : 'No'; ?></td>
                        <td><?php echo $plan['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                        <td>
                            <a href="plan-edit.php?id=<?php echo (int)$plan['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="plans.php?toggle=<?php echo (int)$plan['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-on"></i></a>
                            <a href="plans.php?delete=<?php echo (int)$plan['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this plan?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
