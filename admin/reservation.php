<?php
$page_title = 'Reservation | Admin';
require_once __DIR__ . '/includes/header.php';

$has_computer_column = column_exists($conn, 'reservations', 'computer_number');

$query = "
    SELECT r.id, r.reservation_date, r.time_slot, r.purpose, r.status, r.created_at, r.lab_id" .
    ($has_computer_column ? ', r.computer_number' : '') .
    ", u.student_id, u.first_name, u.last_name, l.lab_name, l.total_computers
    FROM reservations r
    INNER JOIN users u ON r.user_id = u.id
    INNER JOIN labs l ON r.lab_id = l.id
    ORDER BY r.status, r.reservation_date DESC, r.created_at DESC
    LIMIT 100
";

$result = $conn->query($query);
$reservations = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">📆</span><span>Reservation</span></div>
        <div class="panel-body">
            <p class="page-desc">Manage lab reservation requests from students.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Lab</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Purpose</th>
                            <?php if ($has_computer_column): ?><th>PC</th><?php endif; ?>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr><td colspan="<?php echo $has_computer_column ? 8 : 7; ?>" class="empty-state">No reservations yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $row): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?><br>
                                        <small><?php echo htmlspecialchars($row['student_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <?php if ($has_computer_column): ?><td><?php echo htmlspecialchars($row['computer_number'] ?? 'Not assigned'); ?></td><?php endif; ?>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] === 'Pending'): ?>
                                            <button class="btn-sm btn-approve" data-reservation-id="<?php echo (int) $row['id']; ?>" data-lab-id="<?php echo (int) $row['lab_id']; ?>" data-lab-total="<?php echo (int) $row['total_computers']; ?>">Approve</button>
                                            <form method="POST" action="<?php echo app_url('admin/handle_reservation.php'); ?>" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?php echo (int) $row['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn-sm btn-reject" onclick="return confirm('Reject this reservation?');">Reject</button>
                                            </form>
                                        <?php elseif ($row['status'] === 'Approved'): ?>
                                            <form method="POST" action="<?php echo app_url('admin/handle_reservation.php'); ?>" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?php echo (int) $row['id']; ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="btn-sm btn-cancel" onclick="return confirm('Cancel this reservation?');">Cancel</button>
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

<div id="assignPcModal" class="admin-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-header">
            <h2>Assign Workstation</h2>
            <button type="button" class="modal-close" aria-label="Close">✕</button>
        </div>

        <form id="assignPcForm" method="POST" action="<?php echo app_url('admin/handle_reservation.php'); ?>">
            <div class="modal-body">
                <input type="hidden" name="reservation_id" id="modal_reservation_id">
                <input type="hidden" name="action" value="approve">

                <div class="form-group">
                    <label>Select Workstation (PC)</label>
                    <div id="pcGridContainer" class="admin-pc-grid"></div>
                </div>

                <input type="hidden" name="computer_number" id="modal_computer_number">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn-primary">Approve & Assign</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('assignPcModal');
    const approveButtons = document.querySelectorAll('.btn-approve');
    const closeButtons = document.querySelectorAll('.modal-close');
    const form = document.getElementById('assignPcForm');
    const pcGridContainer = document.getElementById('pcGridContainer');
    const computerNumberInput = document.getElementById('modal_computer_number');
    const reservationIdInput = document.getElementById('modal_reservation_id');

    let selectedPc = '';

    function openModal(e) {
        const btn = e.target;
        const reservationId = btn.dataset.reservationId;
        const labId = btn.dataset.labId;
        const labTotal = Number(btn.dataset.labTotal);

        reservationIdInput.value = reservationId;
        selectedPc = '';
        computerNumberInput.value = '';

        renderPcGrid(labTotal);
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function renderPcGrid(total) {
        pcGridContainer.innerHTML = '';

        for (let i = 1; i <= total; i++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'pc-btn';
            const label = 'PC-' + String(i).padStart(2, '0');
            button.dataset.pc = label;
            button.textContent = label;

            button.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.pc-btn.selected').forEach(el => el.classList.remove('selected'));
                button.classList.add('selected');
                selectedPc = label;
                computerNumberInput.value = selectedPc;
            });

            pcGridContainer.appendChild(button);
        }
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    approveButtons.forEach(btn => btn.addEventListener('click', openModal));
    closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

    form.addEventListener('submit', function (e) {
        if (!computerNumberInput.value) {
            e.preventDefault();
            alert('Please select a workstation.');
        }
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
