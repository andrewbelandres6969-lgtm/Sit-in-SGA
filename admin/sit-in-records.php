<?php
$page_title = 'Sit-in Records | Admin';
require_once __DIR__ . '/includes/header.php';

$result = $conn->query("
    SELECT s.id, s.purpose, s.status, s.time_in, s.time_out, s.computer_number,
           u.student_id, u.first_name, u.last_name,
           l.lab_name
    FROM sitin_records s
    INNER JOIN users u ON s.user_id = u.id
    INNER JOIN labs l ON s.lab_id = l.id
    ORDER BY s.time_in DESC
    LIMIT 100
");
$records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

function status_class($status) {
    return 'status-' . strtolower($status);
}
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">📋</span><span>View Sit-in Records</span></div>
        <div class="panel-body">
            <p class="page-desc">Complete history of all sit-in sessions.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Lab</th>
                            <th>Purpose</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="6" class="empty-state">No sit-in records yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?><br>
                                        <small><?php echo htmlspecialchars($row['student_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td><?php echo $row['time_in'] ? date('M d, Y h:i A', strtotime($row['time_in'])) : '—'; ?></td>
                                    <td>
                                        <?php if ($row['time_out']): ?>
                                            <?php echo date('M d, Y h:i A', strtotime($row['time_out'])); ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo status_class($row['status']); ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
