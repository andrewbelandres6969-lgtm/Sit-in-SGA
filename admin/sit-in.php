<?php
$page_title = 'Sit-in | Admin';
require_once __DIR__ . '/includes/header.php';

$settings = $conn->query('SELECT sitin_time_limit_minutes FROM settings ORDER BY id DESC LIMIT 1')->fetch_assoc();
$minutes = (int) ($settings['sitin_time_limit_minutes'] ?? 60);
$has_reservation_pc = column_exists($conn, 'reservations', 'computer_number');

if ($has_reservation_pc) {
    $backfill = $conn->prepare("
        INSERT INTO sitin_records (user_id, lab_id, purpose, status, computer_number, time_in, approved_at, session_end)
        SELECT r.user_id, r.lab_id, r.purpose, 'Approved', r.computer_number,
               STR_TO_DATE(CONCAT(r.reservation_date, ' ', r.time_slot), '%Y-%m-%d %H:%i'),
               NOW(),
               DATE_ADD(STR_TO_DATE(CONCAT(r.reservation_date, ' ', r.time_slot), '%Y-%m-%d %H:%i'), INTERVAL ? MINUTE)
        FROM reservations r
        WHERE r.status = 'Approved'
          AND NOT EXISTS (
              SELECT 1
              FROM sitin_records s
              WHERE s.user_id = r.user_id
                AND s.lab_id = r.lab_id
                AND s.purpose = r.purpose
                AND DATE(s.time_in) = r.reservation_date
                AND TIME_FORMAT(s.time_in, '%H:%i') = r.time_slot
                AND IFNULL(s.computer_number, '') = IFNULL(r.computer_number, '')
          )
    ");
} else {
    $backfill = $conn->prepare("
        INSERT INTO sitin_records (user_id, lab_id, purpose, status, time_in, approved_at, session_end)
        SELECT r.user_id, r.lab_id, r.purpose, 'Approved',
               STR_TO_DATE(CONCAT(r.reservation_date, ' ', r.time_slot), '%Y-%m-%d %H:%i'),
               NOW(),
               DATE_ADD(STR_TO_DATE(CONCAT(r.reservation_date, ' ', r.time_slot), '%Y-%m-%d %H:%i'), INTERVAL ? MINUTE)
        FROM reservations r
        WHERE r.status = 'Approved'
          AND NOT EXISTS (
              SELECT 1
              FROM sitin_records s
              WHERE s.user_id = r.user_id
                AND s.lab_id = r.lab_id
                AND s.purpose = r.purpose
                AND DATE(s.time_in) = r.reservation_date
                AND TIME_FORMAT(s.time_in, '%H:%i') = r.time_slot
          )
    ");
}

$backfill->bind_param('i', $minutes);
$backfill->execute();

$result = $conn->query("
    SELECT s.id, s.purpose, s.status, s.time_in, s.computer_number,
           u.student_id, u.first_name, u.last_name, u.course,
           l.lab_name
    FROM sitin_records s
    INNER JOIN users u ON s.user_id = u.id
    INNER JOIN labs l ON s.lab_id = l.id
    WHERE s.status IN ('Pending', 'Approved') AND s.time_out IS NULL
    ORDER BY FIELD(s.status, 'Pending', 'Approved'), s.time_in ASC
");
$sessions = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">💻</span><span>Sit-in Requests</span></div>
        <div class="panel-body">
            <p class="page-desc">Manage pending sit-in requests and approved reservation sessions.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Lab</th>
                            <th>Purpose</th>
                            <th>PC</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sessions)): ?>
                            <tr><td colspan="7" class="empty-state">No pending or approved sit-in sessions.</td></tr>
                        <?php else: ?>
                            <?php foreach ($sessions as $row): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?><br>
                                        <small><?php echo htmlspecialchars($row['student_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td><?php echo htmlspecialchars($row['computer_number'] ?: 'Not assigned'); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($row['time_in'])); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td>
                                        <?php if ($row['status'] === 'Pending'): ?>
                                            <a class="action-link" href="<?php echo app_url('admin/approve_sitin.php?id=' . $row['id'] . '&action=approve'); ?>">Approve</a>
                                            <a class="action-link" href="<?php echo app_url('admin/approve_sitin.php?id=' . $row['id'] . '&action=reject'); ?>" onclick="return confirm('Reject this request?')">Reject</a>
                                        <?php elseif ($row['status'] === 'Approved'): ?>
                                            <form method="POST" action="<?php echo app_url('admin/time_out_sitin.php'); ?>" style="display:inline;">
                                                <input type="hidden" name="record_id" value="<?php echo (int) $row['id']; ?>">
                                                <button type="submit" class="btn-sm btn-timeout" onclick="return confirm('Time out this student now?');">Time Out</button>
                                            </form>
                                        <?php endif; ?>
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
