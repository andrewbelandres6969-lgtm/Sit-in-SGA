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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="7" class="empty-state">No students registered yet.</td></tr>
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
