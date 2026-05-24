<?php
$page_title = 'Students | Admin';
require_once __DIR__ . '/includes/header.php';

$result = $conn->query("
    SELECT id, student_id, first_name, last_name, course, course_level, email, sitin_remaining, created_at
    FROM users
    WHERE role = 'student'
    ORDER BY created_at DESC
");
$students = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">👥</span><span>Students</span></div>
        <div class="panel-body">
            <p class="page-desc">All registered students in the sit-in system.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Email</th>
                            <th>Sit-in Left</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="8" class="empty-state">No students registered yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course_level']); ?> Year</td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo (int) $row['sitin_remaining']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn-sm btn-edit js-edit-student"
                                            data-id="<?php echo (int) $row['id']; ?>"
                                            data-student-id="<?php echo htmlspecialchars($row['student_id']); ?>"
                                            data-first-name="<?php echo htmlspecialchars($row['first_name']); ?>"
                                            data-last-name="<?php echo htmlspecialchars($row['last_name']); ?>"
                                            data-course="<?php echo htmlspecialchars($row['course']); ?>"
                                            data-course-level="<?php echo htmlspecialchars($row['course_level']); ?>"
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                            data-sitin-remaining="<?php echo (int) $row['sitin_remaining']; ?>"
                                        >Edit</button>
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

<div id="editStudentModal" class="admin-modal student-editor-modal" aria-hidden="true">
    <div class="modal-dialog student-editor-dialog">
        <div class="modal-header student-editor-header">
            <div>
                <h2>Edit Student Profile</h2>
                <p>Update the selected student's account details.</p>
            </div>
            <button type="button" class="modal-close" aria-label="Close">✕</button>
        </div>

        <form class="student-editor-form" method="POST" action="<?php echo app_url('admin/update_student.php'); ?>">
            <div class="modal-body student-editor-body">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="student-editor-summary">
                    <div class="student-editor-avatar" id="edit_avatar">--</div>
                    <div>
                        <div class="student-editor-name" id="edit_full_name">Student Name</div>
                        <div class="student-editor-meta" id="edit_meta">Student ID</div>
                    </div>
                </div>

                <div class="student-editor-grid">
                <div class="form-group">
                    <label for="edit_student_id">Student ID</label>
                    <input type="text" id="edit_student_id" name="student_id" required>
                </div>

                <div class="form-group">
                    <label for="edit_first_name">First Name</label>
                    <input type="text" id="edit_first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="edit_last_name">Last Name</label>
                    <input type="text" id="edit_last_name" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="edit_course">Course</label>
                    <select id="edit_course" name="course" required>
                        <option value="BS CS">BS CS</option>
                        <option value="BS IT">BS IT</option>
                        <option value="BS CIS">BS CIS</option>
                        <option value="BSCE">BSCE</option>
                        <option value="BSEE">BSEE</option>
                        <option value="BSME">BSME</option>
                        <option value="BSECE">BSECE</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_course_level">Year Level</label>
                    <select id="edit_course_level" name="course_level" required>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="edit_sitin_remaining">Sit-in Left</label>
                    <input type="number" id="edit_sitin_remaining" name="sitin_remaining" min="0" max="30" required>
                </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('editStudentModal');
    const editButtons = document.querySelectorAll('.js-edit-student');
    const closeButtons = modal.querySelectorAll('.modal-close');

    const fields = {
        userId: document.getElementById('edit_user_id'),
        studentId: document.getElementById('edit_student_id'),
        firstName: document.getElementById('edit_first_name'),
        lastName: document.getElementById('edit_last_name'),
        course: document.getElementById('edit_course'),
        courseLevel: document.getElementById('edit_course_level'),
        email: document.getElementById('edit_email'),
        sitinRemaining: document.getElementById('edit_sitin_remaining'),
        avatar: document.getElementById('edit_avatar'),
        fullName: document.getElementById('edit_full_name'),
        meta: document.getElementById('edit_meta')
    };

    function openModal(button) {
        const firstName = button.dataset.firstName || '';
        const lastName = button.dataset.lastName || '';
        const studentId = button.dataset.studentId || '';
        const course = button.dataset.course || '';
        const courseLevel = button.dataset.courseLevel || '';

        fields.userId.value = button.dataset.id || '';
        fields.studentId.value = studentId;
        fields.firstName.value = firstName;
        fields.lastName.value = lastName;
        fields.course.value = course;
        fields.courseLevel.value = courseLevel;
        fields.email.value = button.dataset.email || '';
        fields.sitinRemaining.value = button.dataset.sitinRemaining || '0';
        fields.avatar.textContent = ((firstName.charAt(0) || '') + (lastName.charAt(0) || '')).toUpperCase() || '--';
        fields.fullName.textContent = `${firstName} ${lastName}`.trim() || 'Student Name';
        fields.meta.textContent = `${studentId} • ${course || 'Course'} • ${courseLevel || '-'} Year`;

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            openModal(button);
        });
    });

    closeButtons.forEach(button => button.addEventListener('click', closeModal));
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
