<?php
/**
 * Admin Enquiries
 * Full enquiry management with filtering, search, status, notes, and CSV export
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Enquiries';
$success = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        if ($_POST['action'] === 'update_status') {
            $id = (int)($_POST['enquiry_id'] ?? 0);
            $status = sanitizeInput($_POST['status'] ?? '');
            $validStatuses = ['new', 'contacted', 'converted', 'closed'];
            if ($id > 0 && in_array($status, $validStatuses)) {
                try {
                    $stmt = $pdo->prepare("UPDATE enquiries SET status = :status WHERE id = :id");
                    $stmt->execute([':status' => $status, ':id' => $id]);
                    $success = 'Status updated.';
                } catch (PDOException $e) {
                    $error = 'Failed to update status.';
                }
            }
        } elseif ($_POST['action'] === 'update_notes') {
            $id = (int)($_POST['enquiry_id'] ?? 0);
            $notes = $_POST['notes'] ?? '';
            if ($id > 0) {
                try {
                    $stmt = $pdo->prepare("UPDATE enquiries SET notes = :notes WHERE id = :id");
                    $stmt->execute([':notes' => $notes, ':id' => $id]);
                    $success = 'Notes updated.';
                } catch (PDOException $e) {
                    $error = 'Failed to update notes.';
                }
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (verifyCsrfToken($_GET['token'] ?? '')) {
        $id = (int)$_GET['delete'];
        try {
            $stmt = $pdo->prepare("DELETE FROM enquiries WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Enquiry deleted.';
        } catch (PDOException $e) {
            $error = 'Failed to delete enquiry.';
        }
    }
}

// Filter and search
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM enquiries WHERE 1=1";
$params = [];

if ($filter === 'contact') {
    $query .= " AND enquiry_type = 'general'";
} elseif ($filter === 'trial') {
    $query .= " AND enquiry_type = 'free_trial'";
}

if (!empty($search)) {
    $query .= " AND (name LIKE :search OR email LIKE :search2 OR phone LIKE :search3)";
    $params[':search'] = '%' . $search . '%';
    $params[':search2'] = '%' . $search . '%';
    $params[':search3'] = '%' . $search . '%';
}

$query .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $enquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    $enquiries = [];
}

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="enquiries_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Date', 'Type', 'Name', 'Phone', 'WhatsApp', 'Email', 'Age', 'Gender', 'Fitness Goal', 'Preferred Time', 'Message', 'Status', 'Notes']);
    foreach ($enquiries as $e) {
        fputcsv($output, [
            $e['id'], $e['created_at'], $e['enquiry_type'], $e['name'], $e['phone'],
            $e['whatsapp'] ?? '', $e['email'] ?? '', $e['age'] ?? '', $e['gender'] ?? '',
            $e['fitness_goal'] ?? '', $e['preferred_time'] ?? '', $e['message'] ?? '',
            $e['status'], $e['notes'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}

$token = generateCsrfToken();
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewItem = null;
if ($viewId > 0) {
    foreach ($enquiries as $e) {
        if ((int)$e['id'] === $viewId) {
            $viewItem = $e;
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

<!-- Filter Tabs and Search -->
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px;">
        <div style="display:flex; gap:10px;">
            <a href="enquiries.php?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
            <a href="enquiries.php?filter=contact" class="btn <?php echo $filter === 'contact' ? 'btn-primary' : 'btn-secondary'; ?>">Contact Enquiries</a>
            <a href="enquiries.php?filter=trial" class="btn <?php echo $filter === 'trial' ? 'btn-primary' : 'btn-secondary'; ?>">Trial Bookings</a>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <form method="GET" style="display:flex; gap:5px;">
                <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
                <input type="text" name="search" placeholder="Search name, email, phone..." value="<?php echo e($search); ?>" style="padding:8px 12px; border:1px solid #ddd; border-radius:5px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
            <a href="enquiries.php?export=csv&filter=<?php echo e($filter); ?>&search=<?php echo e($search); ?>" class="btn btn-success"><i class="fas fa-file-csv"></i> Export CSV</a>
        </div>
    </div>

    <p style="color:#666; margin-bottom:15px;">Showing <?php echo count($enquiries); ?> enquiries</p>

    <?php if ($viewItem): ?>
    <!-- View Details -->
    <div style="background:#f8f9fa; padding:20px; border-radius:8px; margin-bottom:20px;">
        <h4 style="margin-bottom:15px;">Enquiry Details - <?php echo e($viewItem['name']); ?>
            <a href="enquiries.php?filter=<?php echo e($filter); ?>&search=<?php echo e($search); ?>" class="btn btn-secondary btn-sm" style="float:right;"><i class="fas fa-times"></i> Close</a>
        </h4>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
            <div><strong>Date:</strong> <?php echo formatDate($viewItem['created_at'], 'd M Y h:i A'); ?></div>
            <div><strong>Type:</strong> <?php echo e(ucfirst(str_replace('_', ' ', $viewItem['enquiry_type']))); ?></div>
            <div><strong>Name:</strong> <?php echo e($viewItem['name']); ?></div>
            <div><strong>Phone:</strong> <?php echo e($viewItem['phone']); ?></div>
            <div><strong>WhatsApp:</strong> <?php echo e($viewItem['whatsapp'] ?? '-'); ?></div>
            <div><strong>Email:</strong> <?php echo e($viewItem['email'] ?? '-'); ?></div>
            <div><strong>Age:</strong> <?php echo e($viewItem['age'] ?? '-'); ?></div>
            <div><strong>Gender:</strong> <?php echo e(ucfirst($viewItem['gender'] ?? '-')); ?></div>
            <div><strong>Fitness Goal:</strong> <?php echo e($viewItem['fitness_goal'] ?? '-'); ?></div>
            <div><strong>Preferred Time:</strong> <?php echo e($viewItem['preferred_time'] ?? '-'); ?></div>
        </div>
        <?php if (!empty($viewItem['message'])): ?>
            <div style="margin-top:15px;"><strong>Message:</strong><br><?php echo nl2br(e($viewItem['message'])); ?></div>
        <?php endif; ?>

        <!-- Status Update -->
        <form method="POST" style="margin-top:15px; display:flex; gap:10px; align-items:flex-end;">
            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="enquiry_id" value="<?php echo (int)$viewItem['id']; ?>">
            <div>
                <label style="font-weight:600;">Status:</label>
                <select name="status" style="padding:8px; border:1px solid #ddd; border-radius:5px;">
                    <option value="new" <?php echo $viewItem['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="contacted" <?php echo $viewItem['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                    <option value="converted" <?php echo $viewItem['status'] === 'converted' ? 'selected' : ''; ?>>Converted</option>
                    <option value="closed" <?php echo $viewItem['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
        </form>

        <!-- Notes -->
        <form method="POST" style="margin-top:15px;">
            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
            <input type="hidden" name="action" value="update_notes">
            <input type="hidden" name="enquiry_id" value="<?php echo (int)$viewItem['id']; ?>">
            <div class="form-group">
                <label><strong>Notes:</strong></label>
                <textarea name="notes" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"><?php echo e($viewItem['notes'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Notes</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                    <tr><td colspan="7" style="text-align:center;">No enquiries found.</td></tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $e): ?>
                    <tr>
                        <td><?php echo formatDate($e['created_at'], 'd M Y'); ?></td>
                        <td><?php echo e(ucfirst(str_replace('_', ' ', $e['enquiry_type']))); ?></td>
                        <td><strong><?php echo e($e['name']); ?></strong></td>
                        <td><?php echo e($e['phone']); ?></td>
                        <td><?php echo e($e['email'] ?? '-'); ?></td>
                        <td>
                            <?php if ($e['status'] === 'new'): ?>
                                <span class="badge badge-danger">New</span>
                            <?php elseif ($e['status'] === 'contacted'): ?>
                                <span class="badge badge-success">Contacted</span>
                            <?php elseif ($e['status'] === 'converted'): ?>
                                <span class="badge badge-info">Converted</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?php echo e(ucfirst($e['status'])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="enquiries.php?view=<?php echo (int)$e['id']; ?>&filter=<?php echo e($filter); ?>&search=<?php echo e($search); ?>" class="btn btn-primary btn-sm" title="View"><i class="fas fa-eye"></i></a>
                            <a href="enquiries.php?delete=<?php echo (int)$e['id']; ?>&token=<?php echo $token; ?>&filter=<?php echo e($filter); ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this enquiry?')" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
