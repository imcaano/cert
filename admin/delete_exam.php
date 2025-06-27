<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('DELETE FROM exams WHERE id = ?');
    $stmt->execute([$_GET['id']]);
}
header('Location: dashboard.php?success=1');
exit; 