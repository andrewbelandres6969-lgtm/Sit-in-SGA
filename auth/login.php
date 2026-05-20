<?php
require_once __DIR__ . '/../includes/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('login.php'));
    exit();
}

$student_id = trim($_POST['student_id'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare('SELECT id, student_id, password, role, first_name, last_name FROM users WHERE student_id = ?');
$stmt->bind_param('s', $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    redirect_with_message('login.php', 'error', 'Student ID not found.');
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    redirect_with_message('login.php', 'error', 'Invalid password.');
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['student_id'] = $user['student_id'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];

if ($user['role'] === 'admin') {
    header('Location: ' . app_url('admin/index.php'));
    exit();
}

header('Location: ' . app_url('student/index.php'));
exit();
