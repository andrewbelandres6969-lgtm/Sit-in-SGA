<?php
$page_title = 'Reservation | Student';
require_once __DIR__ . '/includes/header.php';

$user_id = (int) $_SESSION['user_id'];
$labs = $conn->query('SELECT id, lab_name, total_computers FROM labs ORDER BY lab_name');
$has_computer_column = column_exists($conn, 'reservations', 'computer_number');

$query = "
    SELECT r.reservation_date, r.time_slot, r.purpose, r.status, l.lab_name" .
    ($has_computer_column ? ', r.computer_number' : '') .
    "\n    FROM reservations r\n    INNER JOIN labs l ON r.lab_id = l.id\n    WHERE r.user_id = ?\n    ORDER BY r.reservation_date DESC\n";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="student-wrap">
    <section class="reservation-grid">
        <div class="reservation-panel reservation-highlight">
            <div>
                <div class="highlight-icon">📅</div>
                <h1>Plan Your Workload</h1>
                <p>Ensure you have a dedicated machine for your programming tasks, thesis development, or personal study.</p>
            </div>

            <div class="reservation-feature-list">
                <div class="feature-pill">
                    <span>✔</span>
                    Guaranteed Slot
                </div>
                <div class="feature-pill">
                    <span>✔</span>
                    Priority Software
                </div>
            </div>
        </div>

        <div class="reservation-panel reservation-form-card">
            <div class="reservation-form-body">
                <h2>New Reservation</h2>
                <p class="page-desc">Select your lab, schedule, and workstation before submitting your request.</p>

                <form class="reservation-form" method="POST" action="<?php echo app_url('student/save_reservation.php'); ?>">
                    <div class="reservation-fields">
                        <div class="form-group">
                            <label for="lab_id">Laboratory Room</label>
                            <select id="lab_id" name="lab_id" required>
                                <option value="">Select Laboratory</option>
                                <?php while ($lab = $labs->fetch_assoc()): ?>
                                    <option value="<?php echo (int) $lab['id']; ?>" data-total="<?php echo (int) $lab['total_computers']; ?>"><?php echo htmlspecialchars($lab['lab_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Sit-In Purpose</label>
                            <select id="purpose" name="purpose" required>
                                <option value="">Select Purpose</option>
                                <option value="Cybersecurity &amp; Ethical Hacking Practice">Cybersecurity & Ethical Hacking Practice</option>
                                <option value="Programming Practice">Programming Practice</option>
                                <option value="Thesis Development">Thesis Development</option>
                                <option value="Research and Reporting">Research and Reporting</option>
                                <option value="Software Testing">Software Testing</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Available Software</label>
                            <div class="software-chips">
                                <span class="chip">Code::Blocks v20.03</span>
                                <span class="chip">Git v2.43.0</span>
                                <span class="chip">Notepad++ v8.6.2</span>
                                <span class="chip">Python v3.12.2</span>
                                <span class="chip">Visual Studio Code v1.87.0</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reservation_date">Preferred Date</label>
                            <input type="date" id="reservation_date" name="reservation_date" required>
                        </div>

                        <div class="form-group">
                            <label for="time_slot">Arrival Time</label>
                            <input type="time" id="time_slot" name="time_slot" required>
                        </div>

                        <div class="workstation-panel">
                            <div>
                                <div class="workstation-label">Preferred Workstation (PC Number)</div>
                                <div class="workstation-selected">Selected: <span id="selected_pc_text">NONE SELECTED</span></div>
                            </div>
                            <button type="button" id="select_pc_button" class="btn-secondary">SELECT PC</button>
                        </div>

                        <input type="hidden" id="computer_number" name="computer_number" value="">
                    </div>

                    <button type="submit" class="btn-student-submit">Submit Reservation Request</button>
                </form>
            </div>
        </div>
    </section>

    <section class="student-page-panel">
        <div class="student-panel-header">My Reservations</div>
        <div class="student-page-body">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Lab</th>
                            <th>Date</th>
                            <th>Time</th>
                            <?php if ($has_computer_column): ?><th>Workstation</th><?php endif; ?>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr><td colspan="<?php echo $has_computer_column ? 6 : 5; ?>" class="empty-note">No reservations yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                    <?php if ($has_computer_column): ?><td><?php echo htmlspecialchars($row['computer_number'] ?: 'None'); ?></td><?php endif; ?>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="pcModal" class="pc-modal" aria-hidden="true">
        <div class="pc-modal-dialog">
            <div class="pc-modal-header">
                <div>
                    <div class="modal-title">Interactive PC Layout</div>
                    <div class="modal-subtitle">Select an available workstation in the chosen lab</div>
                </div>
                <button type="button" class="pc-modal-close" aria-label="Close">✕</button>
            </div>

            <div class="pc-legend-row">
                <div class="pc-legend-item"><span class="legend-dot available"></span>Available</div>
                <div class="pc-legend-item"><span class="legend-dot occupied"></span>Occupied</div>
                <div class="pc-legend-item"><span class="legend-dot selected"></span>Selected</div>
                <div class="pc-status-badge" id="pc_status_badge">No PC selected</div>
            </div>

            <div id="pcGrid" class="pc-grid"></div>

            <div class="pc-modal-actions">
                <button type="button" class="btn-secondary pc-modal-close">Close</button>
            </div>
        </div>
    </div>
</main>

<script>
(function() {
    const labSelect = document.getElementById('lab_id');
    const pcButton = document.getElementById('select_pc_button');
    const modal = document.getElementById('pcModal');
    const pcGrid = document.getElementById('pcGrid');
    const selectedDisplay = document.getElementById('selected_pc_text');
    const computerInput = document.getElementById('computer_number');
    const closeButtons = document.querySelectorAll('.pc-modal-close');
    const statusBadge = document.getElementById('pc_status_badge');

    let selectedPc = computerInput.value || '';

    function renderGrid(total) {
        pcGrid.innerHTML = '';

        for (let i = 1; i <= total; i++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'pc-button';
            const label = 'PC-' + String(i).padStart(2, '0');
            button.dataset.pc = label;
            button.textContent = label;

            if (selectedPc === label) {
                button.classList.add('selected');
            }

            button.addEventListener('click', function () {
                document.querySelectorAll('.pc-button.selected').forEach(el => el.classList.remove('selected'));
                button.classList.add('selected');
                selectedPc = button.dataset.pc;
                computerInput.value = selectedPc;
                updateSelectedText();
                updateStatusBadge();
            });

            pcGrid.appendChild(button);
        }

        updateStatusBadge();
    }

    function updateSelectedText() {
        selectedDisplay.textContent = selectedPc || 'NONE SELECTED';
    }

    function updateStatusBadge() {
        statusBadge.textContent = selectedPc ? `Selected ${selectedPc}` : 'No PC selected';
    }

    function openModal() {
        const selectedOption = labSelect.selectedOptions[0];
        const total = Number(selectedOption?.dataset.total || 0);

        if (!labSelect.value || total <= 0) {
            alert('Please choose a laboratory first.');
            return;
        }

        renderGrid(total);
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    pcButton.addEventListener('click', openModal);
    closeButtons.forEach(button => button.addEventListener('click', closeModal));
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    updateSelectedText();
    updateStatusBadge();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
