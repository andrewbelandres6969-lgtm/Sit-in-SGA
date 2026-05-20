<?php
$page_title = 'Leaderboard | Student';
require_once __DIR__ . '/includes/header.php';

$leaders = get_leaderboard($conn, 15);
?>

<main class="student-wrap">
    <section class="student-page-panel">
        <div class="student-panel-header">Leaderboard</div>
        <div class="student-page-body">
            <h2>Top Students</h2>
            <p class="page-desc">Ranked by completed sit-in sessions.</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Completed Sessions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaders)): ?>
                            <tr><td colspan="4" class="empty-note">No leaderboard data yet.</td></tr>
                        <?php else: ?>
                            <?php $rank = 1; foreach ($leaders as $row): ?>
                                <?php $rankClass = $rank <= 3 ? 'rank-' . $rank : ''; ?>
                                <tr>
                                    <td>
                                        <span class="leaderboard-rank <?php echo $rankClass; ?>"><?php echo $rank; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course'] ?? '—'); ?></td>
                                    <td><?php echo (int) $row['completed_sessions']; ?></td>
                                </tr>
                            <?php $rank++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
