<?php
require_once 'config.php';

header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$eth_address = isset($data['ethereum_address']) ? trim($data['ethereum_address']) : '';
$username = isset($data['username']) ? trim($data['username']) : '';
$role = isset($data['role']) ? trim($data['role']) : 'user';

if (!$eth_address || !$username) {
    echo json_encode(['success' => false, 'message' => 'Ethereum address and username are required.']);
    exit;
}

// Check if address already exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE ethereum_address = ?');
$stmt->execute([$eth_address]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Ethereum address already registered. Please login.']);
    exit;
}

// Insert new user
$stmt = $pdo->prepare('INSERT INTO users (ethereum_address, username, role) VALUES (?, ?, ?)');
try {
    $stmt->execute([$eth_address, $username, $role]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed.']);
} 