<?php
require_once __DIR__ . '/../includes/app.php';
require_role('admin');

$id = (int) ($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    redirect_with_message('admin/sit-in.php', 'error', 'Invalid request.');
}

if ($action === 'reject') {
    $stmt = $conn->prepare("UPDATE sitin_records SET status = 'Rejected' WHERE id = ? AND status = 'Pending'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    redirect_with_message('admin/sit-in.php', 'success', 'Sit-in request rejected.');
}

$settings = $conn->query('SELECT sitin_time_limit_minutes FROM settings ORDER BY id DESC LIMIT 1')->fetch_assoc();
$minutes = (int) ($settings['sitin_time_limit_minutes'] ?? 60);

$stmt = $conn->prepare("
    UPDATE sitin_records
    SET status = 'Approved',
        approved_at = NOW(),
        session_end = DATE_ADD(NOW(), INTERVAL ? MINUTE)
    WHERE id = ? AND status = 'Pending'
");
$stmt->bind_param('ii', $minutes, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Get user_id and decrement their remaining sessions
    $user_stmt = $conn->prepare('SELECT user_id FROM sitin_records WHERE id = ?');
    $user_stmt->bind_param('i', $id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result()->fetch_assoc();
    
    if ($user_result) {
        $user_id = $user_result['user_id'];
        $decrement_stmt = $conn->prepare('UPDATE users SET sitin_remaining = sitin_remaining - 1 WHERE id = ? AND sitin_remaining > 0');
        $decrement_stmt->bind_param('i', $user_id);
        $decrement_stmt->execute();
    }
    
    redirect_with_message('admin/sit-in.php', 'success', 'Sit-in request approved.');
}

redirect_with_message('admin/sit-in.php', 'error', 'Could not approve request.');
