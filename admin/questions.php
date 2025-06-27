<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Questions for <?= htmlspecialchars($exam['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Questions for <?= htmlspecialchars($exam['title']) ?></h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <h2>Add New Question</h2>
    <form method="POST" action="questions_process.php?exam_id=<?= $exam_id ?>">
        <div class="mb-3">
            <label class="form-label">Question Text</label>
            <textarea class="form-control" name="question_text" required></textarea>
        </div>
        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Option A</label>
                <input type="text" class="form-control" name="option_a" required>
            </div>
            <div class="col">
                <label class="form-label">Option B</label>
                <input type="text" class="form-control" name="option_b" required>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Option C</label>
                <input type="text" class="form-control" name="option_c" required>
            </div>
            <div class="col">
                <label class="form-label">Option D</label>
                <input type="text" class="form-control" name="option_d" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Correct Option</label>
            <select class="form-select" name="correct_option" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Question</button>
    </form>
    <h2 class="mt-5">All Questions</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Options</th>
                <th>Correct</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $q): ?>
            <tr>
                <td><?= htmlspecialchars($q['id']) ?></td>
                <td><?= htmlspecialchars($q['question_text']) ?></td>
                <td>
                    A: <?= htmlspecialchars($q['option_a']) ?><br>
                    B: <?= htmlspecialchars($q['option_b']) ?><br>
                    C: <?= htmlspecialchars($q['option_c']) ?><br>
                    D: <?= htmlspecialchars($q['option_d']) ?>
                </td>
                <td><?= htmlspecialchars($q['correct_option']) ?></td>
                <td>
                    <a href="edit_question.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_question.php?id=<?= $q['id'] ?>&exam_id=<?= $exam_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this question?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html> 