<?php
/**
 * Admin Panel Header
 * Includes HTML head, sidebar navigation, and top bar
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$gymName = getSetting('gym_name') ?? 'Gym Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'Admin Panel'); ?> - <?php echo e($gymName); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo @filemtime(__DIR__ . '/../../assets/css/admin.css'); ?>">
</head>
<body>
<div class="admin-panel">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <h2><i class="fas fa-dumbbell"></i> <?php echo e($gymName); ?></h2>
            <small>Admin Panel</small>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="settings.php" class="<?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Site Settings</a></li>
            <li><a href="seo-manager.php" class="<?php echo $currentPage === 'seo-manager.php' ? 'active' : ''; ?>"><i class="fas fa-search"></i> SEO Manager</a></li>
            <li><a href="plans.php" class="<?php echo in_array($currentPage, ['plans.php', 'plan-add.php', 'plan-edit.php']) ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Membership Plans</a></li>
            <li><a href="trainers.php" class="<?php echo in_array($currentPage, ['trainers.php', 'trainer-add.php', 'trainer-edit.php']) ? 'active' : ''; ?>"><i class="fas fa-user-tie"></i> Trainers</a></li>
            <li><a href="classes.php" class="<?php echo in_array($currentPage, ['classes.php', 'class-add.php', 'class-edit.php']) ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Classes</a></li>
            <li><a href="gallery.php" class="<?php echo $currentPage === 'gallery.php' ? 'active' : ''; ?>"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="testimonials.php" class="<?php echo $currentPage === 'testimonials.php' ? 'active' : ''; ?>"><i class="fas fa-star"></i> Testimonials</a></li>
            <li><a href="transformations.php" class="<?php echo $currentPage === 'transformations.php' ? 'active' : ''; ?>"><i class="fas fa-exchange-alt"></i> Transformations</a></li>
            <li><a href="enquiries.php" class="<?php echo $currentPage === 'enquiries.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Enquiries</a></li>
            <li><a href="blog-manager.php" class="<?php echo in_array($currentPage, ['blog-manager.php', 'blog-add.php', 'blog-edit.php']) ? 'active' : ''; ?>"><i class="fas fa-blog"></i> Blog Posts</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="admin-topbar">
            <button class="toggle-sidebar" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <h3><?php echo e($pageTitle ?? 'Admin Panel'); ?></h3>
            <div class="admin-user">
                <span>Welcome, <strong><?php echo e($_SESSION['admin_username'] ?? 'Admin'); ?></strong></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="admin-main">
