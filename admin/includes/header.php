<?php
require_once __DIR__ . '/../../includes/app.php';
require_role('admin');

$admin_page = basename($_SERVER['PHP_SELF']);
$nav_items = [
    'index.php' => ['label' => 'Home', 'url' => 'admin/index.php'],
    'search.php' => ['label' => 'Search', 'url' => 'admin/search.php'],
    'students.php' => ['label' => 'Students', 'url' => 'admin/students.php'],
    'sit-in.php' => ['label' => 'Sit-in', 'url' => 'admin/sit-in.php'],
    'sit-in-records.php' => ['label' => 'View Sit-in Records', 'url' => 'admin/sit-in-records.php'],
    'sit-in-reports.php' => ['label' => 'Sit-in Reports', 'url' => 'admin/sit-in-reports.php'],
    'feedback-reports.php' => ['label' => 'Feedback Reports', 'url' => 'admin/feedback-reports.php'],
    'reservation.php' => ['label' => 'Reservation', 'url' => 'admin/reservation.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin | CCS Sit-In'); ?></title>
    <link rel="stylesheet" href="<?php echo asset_url('assets/css/admin.css'); ?>">
</head>
<body class="admin-body">

<header class="admin-topbar">
    <div class="admin-topbar-inner">
        <div class="admin-brand">College of Computer Studies Admin</div>

        <nav class="admin-nav">
            <?php foreach ($nav_items as $file => $item): ?>
                <a href="<?php echo app_url($item['url']); ?>" class="<?php echo $admin_page === $file ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <a href="<?php echo app_url('auth/logout.php'); ?>" class="btn-logout">Log out</a>
    </div>
</header>

<?php if (!empty($_GET['success'])): ?>
    <div class="admin-flash success admin-wrap"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="admin-flash error admin-wrap"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>
