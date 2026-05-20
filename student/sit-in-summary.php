<?php
$page_title = 'Sit-In Summary | Student';
require_once __DIR__ . '/includes/header.php';

$user = get_logged_in_student($conn);
$summary = get_student_sitin_summary($conn, (int) $user['id']);
?>

<main class="student-wrap">
    <section class="student-page-panel">
        <div class="student-panel-header">Sit-In Summary</div>
        <div class="student-page-body">
            <h2>Your Sit-In Overview</h2>
            <p class="page-desc">Summary of all your sit-in activity.</p>

            <div class="summary-grid">
                <?php
                $cards = [
                    ['total', 'Total Requests'],
                    ['pending', 'Pending'],
                    ['approved', 'Approved'],
                    ['completed', 'Completed'],
                    ['rejected', 'Rejected'],
                    ['expired', 'Expired'],
                ];
                foreach ($cards as [$key, $label]):
                ?>
                <div class="summary-card">
                    <div class="num"><?php echo (int) ($summary[$key] ?? 0); ?></div>
                    <div class="lbl"><?php echo $label; ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="sessions-box" style="margin:0;max-width:320px;">
                Remaining Sessions: <strong><?php echo (int) $user['sitin_remaining']; ?></strong> / 30
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
