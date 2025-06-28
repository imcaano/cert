<?php
require_once 'config.php';

header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$eth_address = isset($data['ethereum_address']) ? $data['ethereum_address'] : '';

if (!$eth_address) {
    echo json_encode(['success' => false, 'message' => 'No Ethereum address provided.']);
    exit;
}

// Check if user exists
$stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE ethereum_address = ?');
$stmt->execute([$eth_address]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    if ($user['role'] === 'admin') {
        $redirect = '/cert/admin/dashboard.php';
    } else {
        // Try to find matching student and set student_id for dashboard compatibility
        $stmt2 = $pdo->prepare('SELECT id FROM students WHERE metamask_address = ?');
        $stmt2->execute([$eth_address]);
        $student = $stmt2->fetch();
        if ($student) {
            $_SESSION['student_id'] = $student['id'];
        }
        $redirect = '/cert/user/dashboard.php';
    }
    // Debug log
    error_log("LOGIN: eth_address={$eth_address}, role={$user['role']}, redirect={$redirect}");
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    error_log("LOGIN FAILED: eth_address={$eth_address} not found");
    echo json_encode(['success' => false, 'message' => 'Ethereum address not registered. Please register first.']);
} 