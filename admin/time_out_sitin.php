<?php
require_once __DIR__ . '/../includes/app.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('admin/sit-in-records.php'));
    exit();
}

$record_id = (int) ($_POST['record_id'] ?? 0);

if ($record_id <= 0) {
    redirect_with_message('admin/sit-in-records.php', 'error', 'Invalid sit-in record.');
}

$stmt = $conn->prepare("
    UPDATE sitin_records
    SET time_out = NOW(),
        status = 'Completed'
    WHERE id = ?
      AND status = 'Approved'
      AND time_out IS NULL
");
$stmt->bind_param('i', $record_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    redirect_with_message('admin/sit-in-records.php', 'success', 'Student timed out successfully.');
}

redirect_with_message('admin/sit-in-records.php', 'error', 'This record is already timed out or cannot be timed out.');
