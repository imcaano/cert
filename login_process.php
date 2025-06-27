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
        $redirect = 'admin/dashboard.php';
    } else {
        $redirect = 'user/dashboard.php';
    }
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ethereum address not registered. Please register first.']);
} 