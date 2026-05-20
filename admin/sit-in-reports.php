<?php
$page_title = 'Sit-in Reports | Admin';
require_once __DIR__ . '/includes/header.php';

$summary = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM sitin_records
    GROUP BY status
")->fetch_all(MYSQLI_ASSOC);

$by_lab = $conn->query("
    SELECT l.lab_name, COUNT(s.id) AS total
    FROM sitin_records s
    INNER JOIN labs l ON s.lab_id = l.id
    GROUP BY l.lab_name
    ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

$daily = $conn->query("
    SELECT DATE(time_in) AS log_date, COUNT(*) AS total
    FROM sitin_records
    GROUP BY DATE(time_in)
    ORDER BY log_date DESC
    LIMIT 14
")->fetch_all(MYSQLI_ASSOC);
?>

<main class="admin-wrap">
    <section class="page-card" style="margin-bottom:24px;">
        <div class="panel-header"><span class="icon">📈</span><span>Sit-in Reports — Summary</span></div>
        <div class="panel-body">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Status</th><th>Count</th></tr></thead>
                    <tbody>
                        <?php if (empty($summary)): ?>
                            <tr><td colspan="2" class="empty-state">No data yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($summary as $row): ?>
                                <tr>
                                    <td><span class="status-badge <?php echo 'status-' . strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td><?php echo (int) $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="page-card">
            <div class="panel-header"><span class="icon">🏫</span><span>By Laboratory</span></div>
            <div class="panel-body">
                <table class="data-table">
                    <thead><tr><th>Lab</th><th>Total Sit-ins</th></tr></thead>
                    <tbody>
                        <?php foreach ($by_lab as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                <td><?php echo (int) $row['total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($by_lab)): ?>
                            <tr><td colspan="2" class="empty-state">No lab data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="page-card">
            <div class="panel-header"><span class="icon">📅</span><span>Daily Activity (14 days)</span></div>
            <div class="panel-body">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Sit-ins</th></tr></thead>
                    <tbody>
                        <?php foreach ($daily as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['log_date']); ?></td>
                                <td><?php echo (int) $row['total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($daily)): ?>
                            <tr><td colspan="2" class="empty-state">No daily data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
