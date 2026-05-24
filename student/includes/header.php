<?php
require_once __DIR__ . '/../../includes/app.php';
require_student();

$student_page = basename($_SERVER['PHP_SELF']);
$nav_items = [
    'index.php' => ['label' => 'Home', 'url' => 'student/index.php'],
    'edit-profile.php' => ['label' => 'Edit Profile', 'url' => 'student/edit-profile.php'],
    'sit-in-summary.php' => ['label' => 'Sit-In Summary', 'url' => 'student/sit-in-summary.php'],
    'sessions.php' => ['label' => 'Sessions', 'url' => 'student/sessions.php'],
    'history.php' => ['label' => 'History', 'url' => 'student/history.php'],
    'leaderboard.php' => ['label' => 'Leaderboard', 'url' => 'student/leaderboard.php'],
    'reservation.php' => ['label' => 'Reservation', 'url' => 'student/reservation.php'],
    'feedback.php' => ['label' => 'Feedback', 'url' => 'student/feedback.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Dashboard | CCS Sit-In'); ?></title>
    <link rel="stylesheet" href="<?php echo asset_url('assets/css/student.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/student.css'); ?>">
</head>
<body class="student-body">

<header class="student-topbar">
    <div class="student-topbar-inner">
        <a href="<?php echo app_url('student/index.php'); ?>" class="student-brand">
            <?php $school_logo = __DIR__ . '/../../assets/images/school-logo.png'; ?>
            <?php if (file_exists($school_logo)): ?>
                <img src="<?php echo asset_url('assets/images/school-logo.png'); ?>" alt="School logo">
            <?php else: ?>
                <span class="student-brand-logo">CCS</span>
            <?php endif; ?>
            <span>Dashboard</span>
        </a>

        <nav class="student-nav">
            <?php foreach ($nav_items as $file => $item): ?>
                <a href="<?php echo app_url($item['url']); ?>" class="<?php echo $student_page === $file ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="student-top-actions">
            <a href="<?php echo app_url('student/index.php'); ?>" class="notif-btn" title="Announcements">🔔</a>
            <a href="<?php echo app_url('auth/logout.php'); ?>" class="btn-student-logout">⎋ Log Out</a>
        </div>
    </div>
</header>

<?php if (!empty($_GET['success'])): ?>
    <div class="student-flash success student-wrap"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="student-flash error student-wrap"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>
