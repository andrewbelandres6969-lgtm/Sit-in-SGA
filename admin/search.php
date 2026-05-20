<?php
$page_title = 'Search | Admin';
require_once __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$students = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("
        SELECT student_id, first_name, last_name, course, email, sitin_remaining
        FROM users
        WHERE role = 'student'
          AND (student_id LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR course LIKE ? OR email LIKE ?)
        ORDER BY last_name, first_name
        LIMIT 50
    ");
    $stmt->bind_param('sssss', $like, $like, $like, $like, $like);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<main class="admin-wrap">
    <section class="page-card">
        <div class="panel-header"><span class="icon">🔍</span><span>Search Students</span></div>
        <div class="panel-body">
            <form class="search-bar" method="GET">
                <input type="text" name="q" placeholder="Search by ID, name, course, or email" value="<?php echo htmlspecialchars($q); ?>">
                <button type="submit" class="btn-teal">Search</button>
            </form>

            <?php if ($q === ''): ?>
                <p class="page-desc">Enter a keyword to search registered students.</p>
            <?php elseif (empty($students)): ?>
                <p class="empty-state">No students found for "<?php echo htmlspecialchars($q); ?>".</p>
            <?php else: ?>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Email</th>
                                <th>Sit-in Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo (int) $row['sitin_remaining']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
