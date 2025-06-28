<?php
require_once '../config.php';

$certificate = null;
$error_message = '';
$search_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_value = trim($_POST['cert_id']);
    if (!empty($search_value)) {
        // Try by cert_id first
        $stmt = $pdo->prepare("SELECT r.*, u.username as student_name, u.ethereum_address as metamask_address, e.title as subject_name, e.total_marks FROM results r JOIN users u ON r.user_id = u.id JOIN exams e ON r.exam_id = e.id WHERE r.cert_id = ?");
        $stmt->execute([$search_value]);
        $certificate = $stmt->fetch();
        if (!$certificate) {
            // Try by certificate_hash
            $stmt = $pdo->prepare("SELECT r.*, u.username as student_name, u.ethereum_address as metamask_address, e.title as subject_name, e.total_marks FROM results r JOIN users u ON r.user_id = u.id JOIN exams e ON r.exam_id = e.id WHERE r.certificate_hash = ?");
            $stmt->execute([$search_value]);
            $certificate = $stmt->fetch();
        }
        if (!$certificate) {
            $error_message = 'Certificate not found. Please check the Certificate ID or Blockchain Hash.';
        }
    } else {
        $error_message = 'Please enter a Certificate ID or Blockchain Hash.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 60%, #e3e9ff 100%);
        }
        .glass-navbar {
            background: rgba(255,255,255,0.85) !important;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            backdrop-filter: blur(8px);
            border-radius: 0 0 24px 24px;
            transition: background 0.3s;
        }
        .navbar-brand span {
            color: #2563eb;
        }
        .navbar-logo {
            width: 38px;
            margin-right: 10px;
            vertical-align: middle;
            filter: drop-shadow(0 2px 6px #2563eb33);
        }
        .navbar .nav-link {
            color: #222 !important;
            font-weight: 500;
            margin-left: 10px;
            position: relative;
            transition: color 0.2s;
        }
        .navbar .nav-link.active, .navbar .nav-link:focus {
            color: #2563eb !important;
        }
        .navbar .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #2563eb;
            transition: width 0.3s;
            position: absolute;
            left: 0; bottom: 0;
        }
        .navbar .nav-link:hover::after {
            width: 100%;
        }
        .navbar .nav-link:hover {
            color: #2563eb !important;
        }
        .login-btn {
            background: linear-gradient(90deg, #2563eb 60%, #6366f1 100%);
            color: #fff !important;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 28px;
            margin-left: 18px;
            box-shadow: 0 2px 8px #2563eb22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .login-btn:hover {
            background: linear-gradient(90deg, #6366f1 60%, #2563eb 100%);
            color: #fff !important;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 18px #6366f144;
        }
        .verify-section {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verify-card {
            max-width: 420px;
            margin: 0 auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.12);
            border: none;
            animation: fadeInUp 1s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .verify-title {
            font-weight: 700;
            color: #2563eb;
        }
        .verify-input {
            font-size: 1.15rem;
            border-radius: 8px;
            padding: 14px 12px;
            border: 1.5px solid #e3e9ff;
            box-shadow: 0 2px 8px #2563eb11;
            transition: border 0.2s;
        }
        .verify-input:focus {
            border: 1.5px solid #2563eb;
            box-shadow: 0 4px 16px #2563eb22;
        }
        .verify-btn {
            background: linear-gradient(90deg, #2563eb 60%, #6366f1 100%);
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px #2563eb22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .verify-btn:hover {
            background: linear-gradient(90deg, #6366f1 60%, #2563eb 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 18px #6366f144;
        }
        .verify-icon {
            font-size: 2.2rem;
            color: #6366f1;
            margin-bottom: 10px;
        }
        .certificate-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 2px 10px rgba(102,126,234,0.07);
        }
        .certificate-id {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #495057;
            background: #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .not-found {
            color: #dc3545;
            font-weight: 600;
            text-align: center;
            margin-top: 20px;
        }
        .verified-badge {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            border-radius: 8px;
            padding: 6px 18px;
            font-weight: 600;
            margin-bottom: 18px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg glass-navbar sticky-top py-2">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="../index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="navbar-logo">
      <span>CertChain</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="../index.php"><i class="bi bi-house-door me-1"></i>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="verify.php"><i class="bi bi-patch-check me-1"></i>Verify Certificate</a>
        </li>
        <li class="nav-item">
          <a class="login-btn nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="verify-section">
  <div class="card verify-card p-4">
    <div class="text-center mb-3">
      <div class="verify-icon"><i class="bi bi-patch-check"></i></div>
      <h2 class="verify-title mb-2">Verify Certificate</h2>
      <div class="text-muted mb-3">Enter the Certificate ID or Blockchain Hash to check authenticity</div>
    </div>
    <form method="POST">
      <div class="mb-3">
        <input type="text" class="form-control verify-input" id="cert_id" name="cert_id" placeholder="Certificate ID or Blockchain Hash" required value="<?php echo htmlspecialchars($search_value); ?>">
      </div>
      <button type="submit" class="btn verify-btn w-100 mb-2"><i class="bi bi-search me-1"></i>Verify</button>
    </form>
    <?php if ($error_message): ?>
      <div class="alert alert-danger mt-3 text-center"><?php echo $error_message; ?></div>
    <?php elseif ($certificate): ?>
      <div class="certificate-details mt-4">
        <div class="verified-badge">
          <i class="bi bi-check-circle me-1"></i>Blockchain Verified
        </div>
        <div class="certificate-id">
          Certificate ID: <?php echo htmlspecialchars($certificate['cert_id']); ?>
        </div>
        <div><strong>Student:</strong> <?php echo htmlspecialchars($certificate['student_name']); ?></div>
        <div><strong>Subject:</strong> <?php echo htmlspecialchars($certificate['subject_name']); ?></div>
        <div><strong>Marks Obtained:</strong> <?php echo $certificate['score']; ?></div>
        <div><strong>Total Marks:</strong> <?php echo $certificate['total_marks']; ?></div>
        <div><strong>Percentage:</strong> <?php echo number_format(($certificate['score'] / $certificate['total_marks']) * 100, 1); ?>%</div>
        <div><strong>Date Issued:</strong> <?php echo date('F j, Y', strtotime($certificate['date_issued'])); ?></div>
        <div><strong>Blockchain Hash:</strong> <span style="font-family:monospace; color:#2563eb;"><?php echo $certificate['certificate_hash']; ?></span></div>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer class="bg-primary text-white text-center py-4 mt-5">
  &copy; 2024 <span class="fw-bold">CertChain</span>. All rights reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 