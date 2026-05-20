<?php
$page_title = 'Admin Home | CCS Sit-In';
require_once __DIR__ . '/includes/header.php';

$stats = get_admin_stats($conn);
$chart = get_course_chart_data($conn);
$announcements = get_announcements($conn, 10);
?>

<main class="admin-wrap">
    <div class="dashboard-grid">
        <section class="admin-panel">
            <div class="panel-header">
                <span class="icon">📊</span>
                <span>Statistics</span>
            </div>
            <div class="panel-body">
                <ul class="stat-list">
                    <li>Students Registered: <strong><?php echo $stats['students_registered']; ?></strong></li>
                    <li>Currently Sit-in: <strong><?php echo $stats['currently_sitin']; ?></strong></li>
                    <li>Total Sit-in: <strong><?php echo $stats['total_sitin']; ?></strong></li>
                </ul>
                <div class="chart-wrap">
                    <canvas id="courseChart"></canvas>
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <div class="panel-header">
                <span class="icon">📢</span>
                <span>Announcement</span>
            </div>
            <div class="panel-body">
                <form method="POST" action="<?php echo app_url('admin/post_announcement.php'); ?>">
                    <label class="announce-label" for="content">New Announcement</label>
                    <textarea class="announce-textarea" id="content" name="content" required></textarea>
                    <button type="submit" class="btn-submit-announce">Submit</button>
                </form>

                <h3 class="posted-title">Posted Announcement</h3>
                <?php if (empty($announcements)): ?>
                    <p class="empty-state" style="padding:20px 0;">No announcements posted yet.</p>
                <?php else: ?>
                    <?php foreach ($announcements as $item): ?>
                        <article class="announcement-item">
                            <div class="announcement-meta">
                                <?php echo htmlspecialchars($item['author_name']); ?>
                                | <?php echo date('Y-M-d', strtotime($item['created_at'])); ?>
                            </div>
                            <div class="announcement-content"><?php echo htmlspecialchars($item['content']); ?></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('courseChart');
if (ctx) {
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($chart['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($chart['values']); ?>,
                backgroundColor: ['#4e79a7', '#f28ea8', '#e15759', '#edc948', '#0cc1a1', '#76b7b2', '#59a14f', '#af7aa1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
