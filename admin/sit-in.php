<?php
$page_title = 'Sit-in | Admin';
require_once __DIR__ . '/includes/header.php';

$result = $conn->query("
    SELECT s.id, s.purpose, s.status, s.time_in, s.computer_number,
           u.student_id, u.first_name, u.last_name, u.course,
           l.lab_name
    FROM sitin_records s
    INNER JOIN users u ON s.user_id = u.id
    INNER JOIN labs l ON s.lab_id = l.id
    WHERE s.status = 'Pending'
    ORDER BY s.time_in ASC
");
$pending = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">💻</span><span>Sit-in Requests</span></div>
        <div class="panel-body">
            <p class="page-desc">Approve or reject pending sit-in requests from students.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Lab</th>
                            <th>Purpose</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending)): ?>
                            <tr><td colspan="6" class="empty-state">No pending sit-in requests.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pending as $row): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?><br>
                                        <small><?php echo htmlspecialchars($row['student_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($row['time_in'])); ?></td>
                                    <td><span class="status-badge status-pending"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td>
                                        <a class="action-link" href="<?php echo app_url('admin/approve_sitin.php?id=' . $row['id'] . '&action=approve'); ?>">Approve</a>
                                        <a class="action-link" href="<?php echo app_url('admin/approve_sitin.php?id=' . $row['id'] . '&action=reject'); ?>" onclick="return confirm('Reject this request?')">Reject</a>
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
