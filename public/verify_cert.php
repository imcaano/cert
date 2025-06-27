<?php
require_once '../config.php';
$verified = null;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cert_id = strtoupper(trim($_POST['cert_id']));
    // Find result by cert_id
    $stmt = $pdo->prepare('SELECT r.*, u.username, e.title FROM results r JOIN users u ON r.user_id = u.id JOIN exams e ON r.exam_id = e.id');
    $stmt->execute();
    $found = false;
    while ($row = $stmt->fetch()) {
        $expected_id = strtoupper(substr(md5($row['user_id'] . $row['exam_id'] . $row['date_issued']), 0, 10));
        if ($expected_id === $cert_id) {
            $found = true;
            $cert_data = $row['username'] . '|' . $row['title'] . '|' . $row['score'] . '|' . $row['date_issued'] . '|' . $cert_id;
            $expected_hash = hash('sha256', $cert_data);
            if ($expected_hash === $row['certificate_hash']) {
                $verified = true;
                $message = 'Certificate is VALID. Blockchain Hash: ' . $row['certificate_hash'];
            } else {
                $verified = false;
                $message = 'Certificate hash mismatch. Certificate is INVALID!';
            }
            break;
        }
    }
    if (!$found) {
        $verified = false;
        $message = 'Certificate ID not found.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Certificate - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1>Verify Certificate</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="cert_id" class="form-label">Certificate ID</label>
            <input type="text" class="form-control" id="cert_id" name="cert_id" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify</button>
    </form>
    <?php if ($verified !== null): ?>
        <div class="alert <?= $verified ? 'alert-success' : 'alert-danger' ?> mt-4">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html> 