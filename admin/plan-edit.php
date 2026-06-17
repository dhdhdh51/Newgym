<?php
/**
 * Admin Edit Plan
 * Edit an existing membership plan
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Edit Membership Plan';
$success = '';
$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('plans.php');
}

// Get plan data
try {
    $stmt = $pdo->prepare("SELECT * FROM membership_plans WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $plan = $stmt->fetch();
    if (!$plan) {
        redirect('plans.php');
    }
} catch (PDOException $e) {
    redirect('plans.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $planName = sanitizeInput($_POST['plan_name'] ?? '');
        $monthlyPrice = (float)($_POST['monthly_price'] ?? 0);
        $quarterlyPrice = (float)($_POST['quarterly_price'] ?? 0);
        $yearlyPrice = (float)($_POST['yearly_price'] ?? 0);
        $featuresRaw = $_POST['features'] ?? '';
        $isHighlighted = isset($_POST['is_highlighted']) ? 1 : 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $featuresArray = array_filter(array_map('trim', explode("\n", $featuresRaw)));
        $featuresJson = json_encode(array_values($featuresArray));

        if (empty($planName) || $monthlyPrice <= 0) {
            $error = 'Plan name and monthly price are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE membership_plans SET plan_name = :name, monthly_price = :monthly, quarterly_price = :quarterly, yearly_price = :yearly, features = :features, is_highlighted = :highlighted, is_active = :active WHERE id = :id");
                $stmt->execute([
                    ':name' => $planName,
                    ':monthly' => $monthlyPrice,
                    ':quarterly' => $quarterlyPrice,
                    ':yearly' => $yearlyPrice,
                    ':features' => $featuresJson,
                    ':highlighted' => $isHighlighted,
                    ':active' => $isActive,
                    ':id' => $id
                ]);
                $success = 'Plan updated successfully!';
                // Refresh plan data
                $stmt = $pdo->prepare("SELECT * FROM membership_plans WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $plan = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Failed to update plan.';
            }
        }
    }
}

// Decode features for textarea display
$featuresDisplay = '';
$features = json_decode($plan['features'] ?? '[]', true);
if (is_array($features)) {
    $featuresDisplay = implode("\n", $features);
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
    <h3 class="section-title">Edit Plan: <?php echo e($plan['plan_name']); ?></h3>
    <a href="plans.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Plans</a>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="plan_name">Plan Name *</label>
            <input type="text" id="plan_name" name="plan_name" required value="<?php echo e($plan['plan_name']); ?>">
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="monthly_price">Monthly Price *</label>
                <input type="number" id="monthly_price" name="monthly_price" step="0.01" required value="<?php echo e($plan['monthly_price']); ?>">
            </div>
            <div class="form-group">
                <label for="quarterly_price">Quarterly Price</label>
                <input type="number" id="quarterly_price" name="quarterly_price" step="0.01" value="<?php echo e($plan['quarterly_price']); ?>">
            </div>
            <div class="form-group">
                <label for="yearly_price">Yearly Price</label>
                <input type="number" id="yearly_price" name="yearly_price" step="0.01" value="<?php echo e($plan['yearly_price']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="features">Features (one per line)</label>
            <textarea id="features" name="features" rows="6"><?php echo e($featuresDisplay); ?></textarea>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_highlighted" value="1" <?php echo $plan['is_highlighted'] ? 'checked' : ''; ?>> Highlighted (Featured Plan)</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo $plan['is_active'] ? 'checked' : ''; ?>> Active</label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Plan</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
