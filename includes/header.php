<?php
require_once __DIR__ . '/app.php';

$page = current_page();
$is_home = $page === 'index.php';
$is_login = $page === 'login.php';
$is_register = $page === 'register.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'CCS Sit-In Monitoring System'); ?></title>
    <link rel="stylesheet" href="<?php echo asset_url('assets/css/style.css'); ?>">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo app_url('index.php'); ?>" class="brand">
            <span class="brand-logo">CCS</span>
            <span class="brand-title">CCS Sit-In Monitoring System</span>
        </a>

        <nav class="main-nav">
            <a href="<?php echo app_url('index.php'); ?>" class="<?php echo $is_home ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo app_url('index.php#community'); ?>">Community</a>
            <a href="<?php echo app_url('index.php#about'); ?>">About Us</a>
        </nav>

        <div class="header-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo app_url('auth/logout.php'); ?>" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="<?php echo app_url('login.php'); ?>" class="btn btn-outline <?php echo $is_login ? 'is-active' : ''; ?>">Login</a>
                <a href="<?php echo app_url('register.php'); ?>" class="btn btn-primary <?php echo $is_register ? 'is-active' : ''; ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!empty($_GET['success'])): ?>
    <div class="flash flash-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="flash flash-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>
