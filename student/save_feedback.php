<?php
require_once __DIR__ . '/../includes/app.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('student/feedback.php'));
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$rating = (int) ($_POST['rating'] ?? 0);
$category = trim($_POST['category'] ?? 'General');
$message = trim($_POST['message'] ?? '');

if ($rating < 0 || $rating > 5) {
    redirect_with_message('student/feedback.php', 'error', 'Rating must be between 0 and 5.');
}

if ($message === '') {
    redirect_with_message('student/feedback.php', 'error', 'Please write a comment before submitting.');
}

if (!column_exists($conn, 'feedback', 'rating')) {
    $conn->query('ALTER TABLE feedback ADD COLUMN rating TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER category');
}

$stmt = $conn->prepare('INSERT INTO feedback (user_id, category, rating, message) VALUES (?, ?, ?, ?)');
$stmt->bind_param('isis', $user_id, $category, $rating, $message);

if ($stmt->execute()) {
    redirect_with_message('student/feedback.php', 'success', 'Feedback submitted successfully.');
}

redirect_with_message('student/feedback.php', 'error', 'Failed to submit feedback.');
