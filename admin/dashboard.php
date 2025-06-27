<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
// Fetch all exams
$exams = $pdo->query('SELECT * FROM exams ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1>Admin Dashboard</h1>
        <a href="create_exam.php" class="btn btn-success mb-3">Create New Exam</a>
        <a href="../logout.php" class="btn btn-secondary mb-3 float-end">Logout</a>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Exam created successfully!</div>
        <?php endif; ?>
        <h2>All Exams</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Total Marks</th>
                    <th>Duration (min)</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                <tr>
                    <td><?= htmlspecialchars($exam['id']) ?></td>
                    <td><?= htmlspecialchars($exam['title']) ?></td>
                    <td><?= htmlspecialchars($exam['total_marks']) ?></td>
                    <td><?= htmlspecialchars($exam['duration_minutes']) ?></td>
                    <td><?= htmlspecialchars($exam['created_at']) ?></td>
                    <td>
                        <a href="edit_exam.php?id=<?= $exam['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_exam.php?id=<?= $exam['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this exam?')">Delete</a>
                        <a href="questions.php?exam_id=<?= $exam['id'] ?>" class="btn btn-sm btn-info">Questions</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 