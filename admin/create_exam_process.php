<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $total_marks = intval($_POST['total_marks']);
    $duration_minutes = intval($_POST['duration_minutes']);
    $stmt = $pdo->prepare('INSERT INTO exams (title, description, total_marks, duration_minutes) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $description, $total_marks, $duration_minutes]);
    header('Location: create_exam.php?success=1');
    exit;
}
header('Location: create_exam.php');
exit; 