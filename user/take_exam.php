<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if (!$exam_id) {
    header('Location: dashboard.php');
    exit;
}
// Fetch exam info
$exam = $pdo->prepare('SELECT * FROM exams WHERE id = ?');
$exam->execute([$exam_id]);
$exam = $exam->fetch();
if (!$exam) {
    header('Location: dashboard.php');
    exit;
}
// Fetch questions
$questions = $pdo->prepare('SELECT * FROM questions WHERE exam_id = ?');
$questions->execute([$exam_id]);
$questions = $questions->fetchAll();
if (!$questions) {
    echo '<div class="container py-5"><h2>No questions found for this exam.</h2></div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Exam - <?= htmlspecialchars($exam['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Take Exam: <?= htmlspecialchars($exam['title']) ?></h1>
    <form method="POST" action="process_exam.php?exam_id=<?= $exam_id ?>">
        <?php foreach ($questions as $i => $q): ?>
        <div class="mb-4">
            <h5>Q<?= $i+1 ?>. <?= htmlspecialchars($q['question_text']) ?></h5>
            <?php foreach (['A','B','C','D'] as $opt): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answers[<?= $q['id'] ?>]" id="q<?= $q['id'] ?>_<?= $opt ?>" value="<?= $opt ?>" required>
                    <label class="form-check-label" for="q<?= $q['id'] ?>_<?= $opt ?>">
                        <?= htmlspecialchars($q['{"option_" . strtolower($opt)}']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-success">Submit Exam</button>
    </form>
</div>
</body>
</html> 