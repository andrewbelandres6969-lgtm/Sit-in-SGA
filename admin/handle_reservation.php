<?php
require_once __DIR__ . '/../includes/app.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('admin/reservation.php'));
    exit();
}

$reservation_id = (int) ($_POST['reservation_id'] ?? 0);
$action = trim($_POST['action'] ?? '');
$computer_number = trim($_POST['computer_number'] ?? '');

if ($reservation_id <= 0 || !in_array($action, ['approve', 'reject', 'cancel'])) {
    redirect_with_message('admin/reservation.php', 'error', 'Invalid request.');
}

if ($action === 'approve' && $computer_number === '') {
    redirect_with_message('admin/reservation.php', 'error', 'Please select a workstation.');
}

$has_computer_column = column_exists($conn, 'reservations', 'computer_number');

if ($action === 'approve') {
    $status = 'Approved';
    
    // Get user_id from reservation before updating
    $user_stmt = $conn->prepare('SELECT user_id FROM reservations WHERE id = ?');
    $user_stmt->bind_param('i', $reservation_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result()->fetch_assoc();
    $reservation_user_id = $user_result ? $user_result['user_id'] : null;
    
    if ($has_computer_column) {
        $stmt = $conn->prepare('UPDATE reservations SET status = ?, computer_number = ? WHERE id = ?');
        $stmt->bind_param('ssi', $status, $computer_number, $reservation_id);
    } else {
        $stmt = $conn->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $reservation_id);
    }
    
    $message = 'Reservation approved and workstation assigned.';
} elseif ($action === 'reject') {
    $status = 'Rejected';
    $stmt = $conn->prepare('UPDATE reservations SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $reservation_id);
    $message = 'Reservation rejected.';
} elseif ($action === 'cancel') {
    $status = 'Cancelled';
    $stmt = $conn->prepare('UPDATE reservations SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $reservation_id);
    $message = 'Reservation cancelled.';
}

if ($stmt->execute()) {
    // If approved and user found, decrement remaining sessions
    if ($action === 'approve' && $reservation_user_id) {
        $decrement_stmt = $conn->prepare('UPDATE users SET sitin_remaining = sitin_remaining - 1 WHERE id = ? AND sitin_remaining > 0');
        $decrement_stmt->bind_param('i', $reservation_user_id);
        $decrement_stmt->execute();
    }
    
    redirect_with_message('admin/reservation.php', 'success', $message);
}

redirect_with_message('admin/reservation.php', 'error', 'Failed to update reservation.');
