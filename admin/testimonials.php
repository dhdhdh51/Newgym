<?php
/**
 * Admin Testimonials
 * Manage client testimonials
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Testimonials';
$success = '';
$error = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $clientName = sanitizeInput($_POST['client_name'] ?? '');
        $photo = sanitizeInput($_POST['photo'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $review = $_POST['review'] ?? '';
        $city = sanitizeInput($_POST['city'] ?? '');

        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        if (empty($clientName) || empty($review)) {
            $error = 'Client name and review are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO testimonials (client_name, photo, rating, review, city) VALUES (:name, :photo, :rating, :review, :city)");
                $stmt->execute([':name' => $clientName, ':photo' => $photo, ':rating' => $rating, ':review' => $review, ':city' => $city]);
                $success = 'Testimonial added successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to add testimonial.';
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
        $clientName = sanitizeInput($_POST['client_name'] ?? '');
        $photo = sanitizeInput($_POST['photo'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $review = $_POST['review'] ?? '';
        $city = sanitizeInput($_POST['city'] ?? '');

        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        if (empty($clientName) || empty($review) || $editId <= 0) {
            $error = 'Client name and review are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE testimonials SET client_name = :name, photo = :photo, rating = :rating, review = :review, city = :city WHERE id = :id");
                $stmt->execute([':name' => $clientName, ':photo' => $photo, ':rating' => $rating, ':review' => $review, ':city' => $city, ':id' => $editId]);
                $success = 'Testimonial updated successfully!';
            } catch (PDOException $e) {
                $error = 'Failed to update testimonial.';
            }
        }
    }
}

// Handle toggle active
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Testimonial status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update status.';
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
            $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Testimonial deleted.';
        } catch (PDOException $e) {
            $error = 'Failed to delete testimonial.';
        }
    }
}

// Get all testimonials
try {
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $testimonials = [];
}

$token = generateCsrfToken();
$editItem = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    foreach ($testimonials as $t) {
        if ((int)$t['id'] === (int)$_GET['edit']) {
            $editItem = $t;
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

<!-- Add / Edit Form -->
<div class="admin-card">
    <h3 class="section-title"><?php echo $editItem ? 'Edit Testimonial' : 'Add New Testimonial'; ?></h3>
    <?php if ($editItem): ?>
        <a href="testimonials.php" class="btn btn-secondary btn-sm" style="margin-bottom:15px;"><i class="fas fa-times"></i> Cancel Edit</a>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <input type="hidden" name="action" value="<?php echo $editItem ? 'edit' : 'add'; ?>">
        <?php if ($editItem): ?>
            <input type="hidden" name="edit_id" value="<?php echo (int)$editItem['id']; ?>">
        <?php endif; ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label for="client_name">Client Name *</label>
                <input type="text" id="client_name" name="client_name" required value="<?php echo e($editItem['client_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="photo">Photo URL</label>
                <input type="text" id="photo" name="photo" placeholder="https://..." value="<?php echo e($editItem['photo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="rating">Rating (1-5)</label>
                <select id="rating" name="rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo (isset($editItem['rating']) && (int)$editItem['rating'] === $i) ? 'selected' : ''; ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?php echo e($editItem['city'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="review">Review *</label>
            <textarea id="review" name="review" rows="4" required><?php echo e($editItem['review'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editItem ? 'Update' : 'Add'; ?> Testimonial</button>
    </form>
</div>

<!-- Testimonials List -->
<div class="admin-card">
    <h3 class="section-title">All Testimonials (<?php echo count($testimonials); ?>)</h3>
    <?php if (empty($testimonials)): ?>
        <p>No testimonials yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>City</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $t): ?>
                    <tr>
                        <td><?php echo (int)$t['id']; ?></td>
                        <td><strong><?php echo e($t['client_name']); ?></strong></td>
                        <td><?php echo renderStars((int)$t['rating']); ?></td>
                        <td><?php echo e(truncateText($t['review'], 80)); ?></td>
                        <td><?php echo e($t['city'] ?? '-'); ?></td>
                        <td><?php echo $t['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                        <td>
                            <a href="testimonials.php?edit=<?php echo (int)$t['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="testimonials.php?toggle=<?php echo (int)$t['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-toggle-on"></i></a>
                            <a href="testimonials.php?delete=<?php echo (int)$t['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this testimonial?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin-footer.php'; ?>
