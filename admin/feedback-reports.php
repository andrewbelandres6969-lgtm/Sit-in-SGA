<?php
$page_title = 'Feedback Reports | Admin';
require_once __DIR__ . '/includes/header.php';

$result = $conn->query("
    SELECT f.id, f.category, f.message, f.created_at,
           u.student_id, u.first_name, u.last_name
    FROM feedback f
    INNER JOIN users u ON f.user_id = u.id
    ORDER BY f.created_at DESC
    LIMIT 100
");
$feedback = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">💬</span><span>Feedback Reports</span></div>
        <div class="panel-body">
            <p class="page-desc">Student feedback submitted through the system.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Category</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feedback)): ?>
                            <tr><td colspan="4" class="empty-state">No feedback submitted yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($feedback as $row): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?><br>
                                        <small><?php echo htmlspecialchars($row['student_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td style="max-width:320px;white-space:pre-wrap;"><?php echo htmlspecialchars($row['message']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
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
