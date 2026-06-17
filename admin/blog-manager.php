<?php
/**
 * Admin Blog Manager
 * List all blog posts with actions
 */
require_once '../config.php';
require_once '../functions.php';
requireLogin();

$pageTitle = 'Blog Posts';
$success = '';
$error = '';

// Handle toggle status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!verifyCsrfToken($_GET['token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $id = (int)$_GET['toggle'];
        try {
            $stmt = $pdo->prepare("SELECT status FROM blog_posts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $current = $stmt->fetchColumn();
            $newStatus = ($current === 'published') ? 'draft' : 'published';
            $stmt = $pdo->prepare("UPDATE blog_posts SET status = :status, published_at = :pub WHERE id = :id");
            $pubDate = ($newStatus === 'published') ? date('Y-m-d H:i:s') : null;
            $stmt->execute([':status' => $newStatus, ':pub' => $pubDate, ':id' => $id]);
            $success = 'Post status updated.';
        } catch (PDOException $e) {
            $error = 'Failed to update post status.';
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
            $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success = 'Post deleted.';
        } catch (PDOException $e) {
            $error = 'Failed to delete post.';
        }
    }
}

// Get all posts
try {
    $posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $posts = [];
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
        <h3 class="section-title" style="margin-bottom:0; border-bottom:none;">All Blog Posts</h3>
        <a href="blog-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Post</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="6" style="text-align:center;">No blog posts found.</td></tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo (int)$post['id']; ?></td>
                        <td><strong><?php echo e(truncateText($post['title'], 50)); ?></strong></td>
                        <td><?php echo e($post['category'] ?? '-'); ?></td>
                        <td>
                            <?php if ($post['status'] === 'published'): ?>
                                <span class="badge badge-success">Published</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDate($post['created_at']); ?></td>
                        <td>
                            <a href="blog-edit.php?id=<?php echo (int)$post['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="blog-manager.php?toggle=<?php echo (int)$post['id']; ?>&token=<?php echo $token; ?>" class="btn btn-secondary btn-sm" title="Toggle Status"><i class="fas fa-toggle-on"></i></a>
                            <a href="blog-manager.php?delete=<?php echo (int)$post['id']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this post?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
