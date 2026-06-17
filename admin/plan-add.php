<?php
/**
 * Admin Add Plan
 * Form to add a new membership plan
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Add Membership Plan';
$success = '';
$error = '';

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

        // Convert features (one per line) to JSON array
        $featuresArray = array_filter(array_map('trim', explode("\n", $featuresRaw)));
        $featuresJson = json_encode(array_values($featuresArray));

        if (empty($planName) || $monthlyPrice <= 0) {
            $error = 'Plan name and monthly price are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO membership_plans (plan_name, monthly_price, quarterly_price, yearly_price, features, is_highlighted, is_active) VALUES (:name, :monthly, :quarterly, :yearly, :features, :highlighted, :active)");
                $stmt->execute([
                    ':name' => $planName,
                    ':monthly' => $monthlyPrice,
                    ':quarterly' => $quarterlyPrice,
                    ':yearly' => $yearlyPrice,
                    ':features' => $featuresJson,
                    ':highlighted' => $isHighlighted,
                    ':active' => $isActive
                ]);
                $success = 'Plan added successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to add plan. Please try again.';
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
    <h3 class="section-title">Add New Plan</h3>
    <a href="plans.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-arrow-left"></i> Back to Plans</a>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label for="plan_name">Plan Name *</label>
            <input type="text" id="plan_name" name="plan_name" required value="<?php echo e($_POST['plan_name'] ?? ''); ?>">
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="monthly_price">Monthly Price *</label>
                <input type="number" id="monthly_price" name="monthly_price" step="0.01" required value="<?php echo e($_POST['monthly_price'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="quarterly_price">Quarterly Price</label>
                <input type="number" id="quarterly_price" name="quarterly_price" step="0.01" value="<?php echo e($_POST['quarterly_price'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="yearly_price">Yearly Price</label>
                <input type="number" id="yearly_price" name="yearly_price" step="0.01" value="<?php echo e($_POST['yearly_price'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="features">Features (one per line)</label>
            <textarea id="features" name="features" rows="6"><?php echo e($_POST['features'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_highlighted" value="1" <?php echo isset($_POST['is_highlighted']) ? 'checked' : ''; ?>> Highlighted (Featured Plan)</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo (!isset($_POST['is_active']) && $_SERVER['REQUEST_METHOD'] !== 'POST') ? 'checked' : (isset($_POST['is_active']) ? 'checked' : ''); ?>> Active</label>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Plan</button>
    </form>
</div>

<?php include 'includes/admin-footer.php'; ?>
