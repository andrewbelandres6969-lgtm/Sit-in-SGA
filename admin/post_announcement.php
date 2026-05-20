<?php
require_once __DIR__ . '/../includes/app.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('admin/index.php'));
    exit();
}

$content = trim($_POST['content'] ?? '');

if ($content === '') {
    redirect_with_message('admin/index.php', 'error', 'Announcement cannot be empty.');
}

$author = $_SESSION['name'] ?? 'CCS Admin';
$stmt = $conn->prepare('INSERT INTO announcements (content, author_name) VALUES (?, ?)');
$stmt->bind_param('ss', $content, $author);

if ($stmt->execute()) {
    redirect_with_message('admin/index.php', 'success', 'Announcement posted successfully.');
}

redirect_with_message('admin/index.php', 'error', 'Failed to post announcement.');
