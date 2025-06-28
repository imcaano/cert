<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CertChain</title>
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
        .profile-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.10);
            padding: 40px 30px;
            max-width: 500px;
            margin: 0 auto;
        }
        .profile-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
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
                <a class="nav-link" href="student_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a class="nav-link" href="student_certificates.php"><i class="fas fa-certificate me-2"></i>Certificates</a>
                <a class="nav-link active" href="student_profile.php"><i class="fas fa-user me-2"></i>Profile</a>
                <a class="nav-link" href="student_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </nav>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="profile-card">
                <div class="profile-icon text-center mb-3">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3 class="fw-bold mb-3 text-center">My Profile</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
                <p><strong>Metamask Address:</strong> <span style="font-family:monospace; color:#2563eb;"><?= htmlspecialchars($student['metamask_address']) ?></span></p>
            </div>
        </div>
    </div>
</div>
</body>
</html> 