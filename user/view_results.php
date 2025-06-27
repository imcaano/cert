<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if (!$exam_id) {
    header('Location: dashboard.php');
    exit;
}
// Fetch exam info
$exam = $pdo->prepare('SELECT * FROM exams WHERE id = ?');
$exam->execute([$exam_id]);
$exam = $exam->fetch();
// Fetch result
$result = $pdo->prepare('SELECT * FROM results WHERE user_id = ? AND exam_id = ? ORDER BY date_issued DESC LIMIT 1');
$result->execute([$user_id, $exam_id]);
$result = $result->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Result - <?= htmlspecialchars($exam['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Exam Result: <?= htmlspecialchars($exam['title']) ?></h1>
    <?php if ($result): ?>
        <p><strong>Score:</strong> <?= htmlspecialchars($result['score']) ?></p>
        <p><strong>Status:</strong> <?= $result['passed'] ? '<span class="text-success">Passed</span>' : '<span class="text-danger">Failed</span>' ?></p>
        <?php if ($result['passed']): ?>
            <a href="download_certificate.php?exam_id=<?= $exam_id ?>" class="btn btn-success">Download Certificate (PDF)</a>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning">No result found for this exam.</div>
    <?php endif; ?>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html> 