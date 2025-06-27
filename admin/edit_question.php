<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$question_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$question_id) {
    header('Location: dashboard.php');
    exit;
}
// Fetch question info
$stmt = $pdo->prepare('SELECT * FROM questions WHERE id = ?');
$stmt->execute([$question_id]);
$question = $stmt->fetch();
if (!$question) {
    header('Location: dashboard.php');
    exit;
}
$exam_id = $question['exam_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = trim($_POST['correct_option']);
    $stmt = $pdo->prepare('UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ?');
    $stmt->execute([$question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $question_id]);
    header('Location: questions.php?exam_id=' . $exam_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Edit Question</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Question Text</label>
            <textarea class="form-control" name="question_text" required><?= htmlspecialchars($question['question_text']) ?></textarea>
        </div>
        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Option A</label>
                <input type="text" class="form-control" name="option_a" value="<?= htmlspecialchars($question['option_a']) ?>" required>
            </div>
            <div class="col">
                <label class="form-label">Option B</label>
                <input type="text" class="form-control" name="option_b" value="<?= htmlspecialchars($question['option_b']) ?>" required>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Option C</label>
                <input type="text" class="form-control" name="option_c" value="<?= htmlspecialchars($question['option_c']) ?>" required>
            </div>
            <div class="col">
                <label class="form-label">Option D</label>
                <input type="text" class="form-control" name="option_d" value="<?= htmlspecialchars($question['option_d']) ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Correct Option</label>
            <select class="form-select" name="correct_option" required>
                <option value="A" <?= $question['correct_option'] == 'A' ? 'selected' : '' ?>>A</option>
                <option value="B" <?= $question['correct_option'] == 'B' ? 'selected' : '' ?>>B</option>
                <option value="C" <?= $question['correct_option'] == 'C' ? 'selected' : '' ?>>C</option>
                <option value="D" <?= $question['correct_option'] == 'D' ? 'selected' : '' ?>>D</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Question</button>
        <a href="questions.php?exam_id=<?= $exam_id ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html> 