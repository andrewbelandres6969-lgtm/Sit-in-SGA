<?php
require_once __DIR__ . '/../includes/app.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('admin/sit-in-records.php'));
    exit();
}

$record_id = (int) ($_POST['record_id'] ?? 0);
$time_in = trim($_POST['time_in'] ?? '');
$time_out = trim($_POST['time_out'] ?? '');

if ($record_id <= 0) {
    redirect_with_message('admin/sit-in-records.php', 'error', 'Invalid record.');
}

if (empty($time_in) || empty($time_out)) {
    redirect_with_message('admin/sit-in-records.php', 'error', 'Both times are required.');
}

// Convert datetime-local format to MySQL datetime format
$time_in_db = date('Y-m-d H:i:s', strtotime($time_in));
$time_out_db = date('Y-m-d H:i:s', strtotime($time_out));

// Validate time order
if (strtotime($time_in_db) >= strtotime($time_out_db)) {
    redirect_with_message('admin/sit-in-records.php', 'error', 'Time In must be before Time Out.');
}

$stmt = $conn->prepare('UPDATE sitin_records SET time_in = ?, time_out = ? WHERE id = ?');
if (!$stmt) {
    redirect_with_message('admin/sit-in-records.php', 'error', 'Database error: ' . $conn->error);
}

$stmt->bind_param('ssi', $time_in_db, $time_out_db, $record_id);

if ($stmt->execute()) {
    redirect_with_message('admin/sit-in-records.php', 'success', 'Sit-in times updated successfully.');
} else {
    redirect_with_message('admin/sit-in-records.php', 'error', 'Failed to update times.');
}

$stmt->close();
