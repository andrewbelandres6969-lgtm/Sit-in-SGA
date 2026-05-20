<?php
$page_title = 'Sessions | Student';
require_once __DIR__ . '/includes/header.php';

$user = get_logged_in_student($conn);
$user_id = (int) $user['id'];

$labs = $conn->query('SELECT id, lab_name, total_computers FROM labs ORDER BY lab_name');

$active_stmt = $conn->prepare("
    SELECT s.*, l.lab_name
    FROM sitin_records s
    INNER JOIN labs l ON s.lab_id = l.id
    WHERE s.user_id = ? AND s.status = 'Approved' AND s.time_out IS NULL
    ORDER BY s.id DESC LIMIT 1
");
$active_stmt->bind_param('i', $user_id);
$active_stmt->execute();
$active = $active_stmt->get_result()->fetch_assoc();
?>

<main class="student-wrap">
    <section class="student-page-panel" style="margin-bottom:24px;">
        <div class="student-panel-header">Request Sit-In Session</div>
        <div class="student-page-body">
            <?php if ((int) $user['sitin_remaining'] <= 0): ?>
                <p class="page-desc" style="color:#c62828;">You have no remaining sit-in sessions.</p>
            <?php elseif ($active): ?>
                <p class="page-desc">You have an active session at <strong><?php echo htmlspecialchars($active['lab_name']); ?></strong>.</p>
            <?php else: ?>
                <form class="student-form" method="POST" action="<?php echo app_url('student/save_sitin.php'); ?>">
                    <div>
                        <label for="lab_id">Laboratory</label>
                        <select id="lab_id" name="lab_id" required>
                            <option value="">Select Laboratory</option>
                            <?php while ($lab = $labs->fetch_assoc()): ?>
                                <option value="<?php echo (int) $lab['id']; ?>">
                                    <?php echo htmlspecialchars($lab['lab_name']); ?> (<?php echo (int) $lab['total_computers']; ?> PCs)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label for="purpose">Purpose</label>
                        <textarea id="purpose" name="purpose" placeholder="e.g. Programming assignment" required></textarea>
                    </div>
                    <button type="submit" class="btn-student-primary">Submit Sit-In Request</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <section class="student-page-panel">
        <div class="student-panel-header">Remaining Sessions</div>
        <div class="student-page-body">
            <div class="sessions-box" style="margin:0;">
                You have <strong><?php echo (int) $user['sitin_remaining']; ?></strong> sit-in sessions remaining out of 30.
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
