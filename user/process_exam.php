<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if (!$exam_id || !isset($_POST['answers'])) {
    header('Location: dashboard.php');
    exit;
}
// Fetch questions and correct answers
$questions = $pdo->prepare('SELECT id, correct_option FROM questions WHERE exam_id = ?');
$questions->execute([$exam_id]);
$questions = $questions->fetchAll();
$score = 0;
$total = count($questions);
foreach ($questions as $q) {
    $qid = $q['id'];
    if (isset($_POST['answers'][$qid]) && $_POST['answers'][$qid] === $q['correct_option']) {
        $score++;
    }
}
$passed = $score >= ceil($total * 0.5) ? 1 : 0; // Pass if >= 50%
// Store result
$stmt = $pdo->prepare('INSERT INTO results (user_id, exam_id, score, passed) VALUES (?, ?, ?, ?)');
$stmt->execute([$user_id, $exam_id, $score, $passed]);
header('Location: view_results.php?exam_id=' . $exam_id);
exit; 