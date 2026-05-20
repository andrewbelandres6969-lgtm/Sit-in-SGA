<?php
$page_title = 'History | Student';
require_once __DIR__ . '/includes/header.php';

$user_id = (int) $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT s.purpose, s.status, s.time_in, s.time_out, l.lab_name
    FROM sitin_records s
    INNER JOIN labs l ON s.lab_id = l.id
    WHERE s.user_id = ?
    ORDER BY s.time_in DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="student-wrap">
    <section class="student-page-panel">
        <div class="student-panel-header">Sit-In History</div>
        <div class="student-page-body">
            <h2>Your Sit-In History</h2>
            <p class="page-desc">All your past and current sit-in records.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Lab</th>
                            <th>Purpose</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="5" class="empty-note">No sit-in history yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($row['time_in'])); ?></td>
                                    <td><?php echo $row['time_out'] ? date('M d, Y h:i A', strtotime($row['time_out'])) : '—'; ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
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
