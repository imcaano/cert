<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
$exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$exam_id) {
    header('Location: dashboard.php');
    exit;
}
// Fetch exam info
$stmt = $pdo->prepare('SELECT * FROM exams WHERE id = ?');
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();
if (!$exam) {
    header('Location: dashboard.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $total_marks = intval($_POST['total_marks']);
    $duration_minutes = intval($_POST['duration_minutes']);
    $stmt = $pdo->prepare('UPDATE exams SET title = ?, description = ?, total_marks = ?, duration_minutes = ? WHERE id = ?');
    $stmt->execute([$title, $description, $total_marks, $duration_minutes, $exam_id]);
    header('Location: dashboard.php?success=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1>Edit Exam</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Exam Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($exam['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($exam['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="total_marks" class="form-label">Total Marks</label>
                <input type="number" class="form-control" id="total_marks" name="total_marks" value="<?= htmlspecialchars($exam['total_marks']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="<?= htmlspecialchars($exam['duration_minutes']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Exam</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html> 