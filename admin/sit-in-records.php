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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="7" class="empty-state">No sit-in records yet.</td></tr>
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
                                    <td><?php echo $row['time_out'] ? date('M d, Y h:i A', strtotime($row['time_out'])) : '—'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo status_class($row['status']); ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-sm btn-edit" data-record-id="<?php echo $row['id']; ?>" data-time-in="<?php echo $row['time_in']; ?>" data-time-out="<?php echo $row['time_out']; ?>">
                                            Edit Times
                                        </button>
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

<!-- Edit Times Modal -->
<div id="editTimesModal" class="admin-modal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h2>Edit Sit-in Times</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form method="POST" action="admin/handle_sitin_times.php">
            <div class="modal-body">
                <div class="form-group">
                    <label for="editTimeIn">Time In</label>
                    <input type="datetime-local" id="editTimeIn" name="time_in" required>
                </div>
                <div class="form-group">
                    <label for="editTimeOut">Time Out</label>
                    <input type="datetime-local" id="editTimeOut" name="time_out" required>
                </div>
                <input type="hidden" id="recordId" name="record_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(recordId, timeIn, timeOut) {
    document.getElementById('recordId').value = recordId;
    
    // Convert database datetime format to datetime-local format
    if (timeIn && timeIn !== 'null') {
        const dateIn = new Date(timeIn);
        const inLocal = dateIn.toISOString().slice(0, 16);
        document.getElementById('editTimeIn').value = inLocal;
    }
    
    if (timeOut && timeOut !== 'null') {
        const dateOut = new Date(timeOut);
        const outLocal = dateOut.toISOString().slice(0, 16);
        document.getElementById('editTimeOut').value = outLocal;
    }
    
    document.getElementById('editTimesModal').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editTimesModal').classList.remove('open');
}

// Add event listeners to edit buttons
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const recordId = this.getAttribute('data-record-id');
        const timeIn = this.getAttribute('data-time-in');
        const timeOut = this.getAttribute('data-time-out');
        openEditModal(recordId, timeIn, timeOut);
    });
});

// Close modal on background click
document.getElementById('editTimesModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
