<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['student_id']) && !isset($_SESSION['user_id'])) {
    header('Location: ../frontend/login.php');
    exit();
}
// Support both session types for compatibility
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();
// Get certificates
$stmt = $pdo->prepare("SELECT r.*, e.title as subject_name, e.total_marks FROM results r JOIN exams e ON r.exam_id = e.id WHERE r.user_id = ?");
$stmt->execute([$student_id]);
$certificates = $stmt->fetchAll();
$cert_count = count($certificates);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6fa; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px 0 0 0;
        }
        .sidebar .nav-link {
            color: #fff;
            font-weight: 500;
            padding: 15px 30px;
            border-radius: 0 30px 30px 0;
            margin-bottom: 10px;
            transition: background 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar .logo {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 40px;
            text-align: center;
        }
        .main-content {
            padding: 40px 30px;
        }
        .welcome-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.10);
            padding: 40px 30px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(102,126,234,0.10);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar d-flex flex-column">
            <div class="logo mb-5">
                <i class="fas fa-graduation-cap me-2"></i>CertChain
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a class="nav-link" href="../frontend/student_certificates.php"><i class="fas fa-certificate me-2"></i>Certificates</a>
                <a class="nav-link" href="../frontend/student_profile.php"><i class="fas fa-user me-2"></i>Profile</a>
                <a class="nav-link" href="../frontend/student_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </nav>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="welcome-card mb-4">
                <h2 class="fw-bold mb-2">Welcome, <?= htmlspecialchars($student['name']) ?>!</h2>
                <p class="mb-0">This is your student dashboard. Use the sidebar to view your certificates or profile.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                        <h3><?= $cert_count ?></h3>
                        <div>Certificates</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-user"></i></div>
                        <h3><?= htmlspecialchars($student['name']) ?></h3>
                        <div>Name</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                        <h3><?= htmlspecialchars($student['email']) ?></h3>
                        <div>Email</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html> 