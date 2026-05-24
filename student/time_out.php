<?php
require_once __DIR__ . '/../includes/app.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('student/sessions.php'));
    exit();
}

$record_id = (int) ($_POST['record_id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];

if ($record_id <= 0) {
    redirect_with_message('student/sessions.php', 'error', 'Invalid session.');
}

$stmt = $conn->prepare("
    UPDATE sitin_records
    SET time_out = NOW(),
        status = 'Completed'
    WHERE id = ?
      AND user_id = ?
      AND status = 'Approved'
      AND time_out IS NULL
");
$stmt->bind_param('ii', $record_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    redirect_with_message('student/history.php', 'success', 'Session timed out successfully.');
}

redirect_with_message('student/sessions.php', 'error', 'This session is already timed out or cannot be timed out.');
