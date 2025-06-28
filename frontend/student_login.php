<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $stmt = $pdo->prepare("SELECT * FROM students WHERE metamask_address = ?");
    $stmt->execute([$address]);
    $student = $stmt->fetch();
    if ($student) {
        $_SESSION['student_id'] = $student['id'];
        header('Location: student_dashboard.php');
        exit();
    } else {
        $error = "Invalid Metamask address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4">
                <h3 class="mb-4 text-center">Student Login</h3>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="address" class="form-label">Metamask Address</label>
                        <input type="text" class="form-control" id="address" name="address" required placeholder="Enter your Metamask address">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html> 