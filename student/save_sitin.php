<?php
require_once __DIR__ . '/../includes/app.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('student/sessions.php'));
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$lab_id = (int) ($_POST['lab_id'] ?? 0);
$purpose = trim($_POST['purpose'] ?? '');

$user = get_logged_in_student($conn);

if ((int) $user['sitin_remaining'] <= 0) {
    redirect_with_message('student/sessions.php', 'error', 'No remaining sit-in sessions.');
}

if ($lab_id <= 0 || $purpose === '') {
    redirect_with_message('student/sessions.php', 'error', 'Please fill in all fields.');
}

$check = $conn->prepare("SELECT id FROM sitin_records WHERE user_id = ? AND status IN ('Pending', 'Approved') AND time_out IS NULL");
$check->bind_param('i', $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    redirect_with_message('student/sessions.php', 'error', 'You already have an active or pending sit-in.');
}

$stmt = $conn->prepare('INSERT INTO sitin_records (user_id, lab_id, purpose, status) VALUES (?, ?, ?, \'Pending\')');
$stmt->bind_param('iis', $user_id, $lab_id, $purpose);

if ($stmt->execute()) {
    redirect_with_message('student/sessions.php', 'success', 'Sit-in request submitted. Wait for admin approval.');
}

redirect_with_message('student/sessions.php', 'error', 'Failed to submit request.');
