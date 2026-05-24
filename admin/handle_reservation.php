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

    $reservation_stmt = $conn->prepare('SELECT user_id, lab_id, reservation_date, time_slot, purpose, status FROM reservations WHERE id = ?');
    $reservation_stmt->bind_param('i', $reservation_id);
    $reservation_stmt->execute();
    $reservation = $reservation_stmt->get_result()->fetch_assoc();

    if (!$reservation || $reservation['status'] !== 'Pending') {
        redirect_with_message('admin/reservation.php', 'error', 'Only pending reservations can be approved.');
    }
    
    if ($has_computer_column) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ?, computer_number = ? WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param('ssi', $status, $computer_number, $reservation_id);
    } else {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param('si', $status, $reservation_id);
    }
    
    $message = 'Reservation approved and moved to Sit-in.';
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
    if ($action === 'approve' && $stmt->affected_rows > 0) {
        $settings = $conn->query('SELECT sitin_time_limit_minutes FROM settings ORDER BY id DESC LIMIT 1')->fetch_assoc();
        $minutes = (int) ($settings['sitin_time_limit_minutes'] ?? 60);
        $reservation_user_id = (int) $reservation['user_id'];
        $reservation_lab_id = (int) $reservation['lab_id'];
        $reservation_purpose = $reservation['purpose'];
        $time_in = date('Y-m-d H:i:s', strtotime($reservation['reservation_date'] . ' ' . $reservation['time_slot']));

        $sitin_stmt = $conn->prepare("
            INSERT INTO sitin_records (user_id, lab_id, purpose, status, computer_number, time_in, approved_at, session_end)
            VALUES (?, ?, ?, 'Approved', ?, ?, NOW(), DATE_ADD(?, INTERVAL ? MINUTE))
        ");
        $sitin_stmt->bind_param(
            'iissssi',
            $reservation_user_id,
            $reservation_lab_id,
            $reservation_purpose,
            $computer_number,
            $time_in,
            $time_in,
            $minutes
        );
        $sitin_stmt->execute();
    }
    
    redirect_with_message('admin/reservation.php', 'success', $message);
}

redirect_with_message('admin/reservation.php', 'error', 'Failed to update reservation.');
