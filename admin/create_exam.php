<?php
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1>Create Exam</h1>
        <form method="POST" action="create_exam_process.php">
            <div class="mb-3">
                <label for="title" class="form-label">Exam Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="total_marks" class="form-label">Total Marks</label>
                <input type="number" class="form-control" id="total_marks" name="total_marks" required>
            </div>
            <div class="mb-3">
                <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Exam</button>
        </form>
    </div>
</body>
</html> 