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
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-panel { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #1d1d1d; color: #fff; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; z-index: 1000; transition: transform 0.3s ease; }
        .admin-sidebar .sidebar-brand { padding: 20px; text-align: center; border-bottom: 1px solid #333; }
        .admin-sidebar .sidebar-brand h2 { color: #e63946; font-size: 1.2rem; margin: 0; }
        .admin-sidebar .sidebar-nav { list-style: none; padding: 10px 0; margin: 0; }
        .admin-sidebar .sidebar-nav li { margin: 2px 0; }
        .admin-sidebar .sidebar-nav li a { display: flex; align-items: center; padding: 12px 20px; color: #ccc; text-decoration: none; transition: all 0.3s; font-size: 0.9rem; }
        .admin-sidebar .sidebar-nav li a:hover, .admin-sidebar .sidebar-nav li a.active { background: #e63946; color: #fff; border-radius: 0 25px 25px 0; margin-right: 10px; }
        .admin-sidebar .sidebar-nav li a i { width: 24px; margin-right: 10px; text-align: center; }
        .admin-content { flex: 1; margin-left: 260px; background: #f4f6f9; min-height: 100vh; }
        .admin-topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 999; }
        .admin-topbar .toggle-sidebar { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #333; }
        .admin-topbar .admin-user { display: flex; align-items: center; gap: 10px; }
        .admin-topbar .admin-user a { color: #e63946; text-decoration: none; font-weight: 500; }
        .admin-main { padding: 30px; }
        .admin-card { background: #fff; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        .admin-table th { background: #f8f9fa; font-weight: 600; color: #333; }
        .admin-table tr:hover { background: #f8f9fa; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; cursor: pointer; border: none; transition: all 0.3s; }
        .btn-primary { background: #e63946; color: #fff; }
        .btn-primary:hover { background: #c5303c; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #e63946; outline: none; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .form-group input[type="color"] { height: 45px; padding: 5px; cursor: pointer; }
        .form-group input[type="checkbox"] { width: auto; margin-right: 8px; }
        .alert { padding: 15px 20px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .stat-card .stat-number { font-size: 2rem; font-weight: 700; color: #e63946; }
        .stat-card .stat-label { color: #666; font-size: 0.9rem; margin-top: 5px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e63946; }
        .quick-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.active { transform: translateX(0); }
            .admin-content { margin-left: 0; }
            .admin-topbar .toggle-sidebar { display: block; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
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
            <button class="toggle-sidebar" onclick="document.getElementById('adminSidebar').classList.toggle('active')">
                <i class="fas fa-bars"></i>
            </button>
            <h3><?php echo e($pageTitle ?? 'Admin Panel'); ?></h3>
            <div class="admin-user">
                <span>Welcome, <strong><?php echo e($_SESSION['admin_username'] ?? 'Admin'); ?></strong></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="admin-main">
