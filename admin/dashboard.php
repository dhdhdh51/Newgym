<?php
/**
 * Admin Dashboard
 * Shows overview stats and recent activity
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Dashboard';

// Get stats
try {
    $totalEnquiries = $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
    $newEnquiries = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")->fetchColumn();
    $trialBookings = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE enquiry_type = 'free_trial'")->fetchColumn();
    $activePlans = $pdo->query("SELECT COUNT(*) FROM membership_plans WHERE is_active = 1")->fetchColumn();
    $activeTrainers = $pdo->query("SELECT COUNT(*) FROM trainers WHERE is_active = 1")->fetchColumn();
    $activeClasses = $pdo->query("SELECT COUNT(*) FROM classes WHERE is_active = 1")->fetchColumn();
    $publishedPosts = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
} catch (PDOException $e) {
    $totalEnquiries = $newEnquiries = $trialBookings = $activePlans = $activeTrainers = $activeClasses = $publishedPosts = 0;
}

// Recent enquiries
try {
    $recentEnquiries = $pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 10")->fetchAll();
} catch (PDOException $e) {
    $recentEnquiries = [];
}

include 'includes/admin-header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$totalEnquiries; ?></div>
        <div class="stat-label">Total Enquiries</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$newEnquiries; ?></div>
        <div class="stat-label">New Enquiries</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$trialBookings; ?></div>
        <div class="stat-label">Trial Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$activePlans; ?></div>
        <div class="stat-label">Membership Plans</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$activeTrainers; ?></div>
        <div class="stat-label">Active Trainers</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$activeClasses; ?></div>
        <div class="stat-label">Active Classes</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo (int)$publishedPosts; ?></div>
        <div class="stat-label">Blog Posts</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h3 class="section-title">Quick Actions</h3>
    <div class="quick-actions">
        <a href="plan-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Plan</a>
        <a href="trainer-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Trainer</a>
        <a href="class-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Class</a>
        <a href="blog-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Blog Post</a>
    </div>
</div>

<!-- Recent Enquiries -->
<div class="admin-card">
    <h3 class="section-title">Recent Enquiries</h3>
    <?php if (empty($recentEnquiries)): ?>
        <p>No enquiries yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentEnquiries as $enquiry): ?>
                    <tr>
                        <td><?php echo formatDate($enquiry['created_at'], 'd M Y'); ?></td>
                        <td><?php echo e(ucfirst(str_replace('_', ' ', $enquiry['enquiry_type']))); ?></td>
                        <td><?php echo e($enquiry['name']); ?></td>
                        <td><?php echo e($enquiry['phone']); ?></td>
                        <td><?php echo e($enquiry['email'] ?? '-'); ?></td>
                        <td>
                            <?php if ($enquiry['status'] === 'new'): ?>
                                <span class="badge badge-danger">New</span>
                            <?php elseif ($enquiry['status'] === 'contacted'): ?>
                                <span class="badge badge-success">Contacted</span>
                            <?php elseif ($enquiry['status'] === 'converted'): ?>
                                <span class="badge badge-info">Converted</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?php echo e(ucfirst($enquiry['status'])); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 15px;">
            <a href="enquiries.php" class="btn btn-secondary">View All Enquiries</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin-footer.php'; ?>
