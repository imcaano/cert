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

// Get certificates with better data
$stmt = $pdo->prepare("SELECT r.*, e.title as subject_name, e.total_marks FROM results r JOIN exams e ON r.exam_id = e.id WHERE r.user_id = ? ORDER BY r.date_issued DESC");
$stmt->execute([$student_id]);
$certificates = $stmt->fetchAll();

// Calculate statistics
$total_certificates = count($certificates);
$total_marks = 0;
$total_obtained = 0;
foreach ($certificates as $cert) {
    $total_marks += $cert['total_marks'];
    $total_obtained += $cert['score'];
}
$overall_percentage = $total_marks > 0 ? ($total_obtained / $total_marks) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Certificates - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            padding: 30px 0 0 0;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar .nav-link {
            color: #fff;
            font-weight: 500;
            padding: 15px 30px;
            border-radius: 0 30px 30px 0;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.2);
            color: #fff;
            border-color: rgba(255,255,255,0.3);
            transform: translateX(5px);
        }
        .sidebar .logo {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 40px;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .main-content {
            padding: 40px 30px;
            background: rgba(255,255,255,0.95);
            min-height: 100vh;
            backdrop-filter: blur(10px);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102,126,234,0.3);
        }
        .stats-row {
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(102,126,234,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        .certificates-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(102,126,234,0.1);
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 20px 15px;
            border: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        .table td {
            padding: 20px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        .table tbody tr:hover {
            background: rgba(102,126,234,0.05);
        }
        .subject-name {
            font-weight: 600;
            color: #333;
        }
        .percentage-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .percentage-excellent { background: #d4edda; color: #155724; }
        .percentage-good { background: #d1ecf1; color: #0c5460; }
        .percentage-average { background: #fff3cd; color: #856404; }
        .percentage-poor { background: #f8d7da; color: #721c24; }
        .cert-hash {
            font-family: 'Courier New', monospace;
            color: #667eea;
            font-size: 0.8rem;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .date-issued {
            color: #666;
            font-size: 0.9rem;
        }
        .marks-display {
            font-weight: 600;
            color: #333;
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
                <a class="nav-link active" href="student_certificates.php"><i class="fas fa-certificate me-2"></i>Certificates</a>
                <a class="nav-link" href="student_profile.php"><i class="fas fa-user me-2"></i>Profile</a>
                <a class="nav-link" href="student_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h2 class="fw-bold mb-2"><i class="fas fa-certificate me-3"></i>My Certificates</h2>
                <p class="mb-0">View and manage your academic achievements and blockchain-verified certificates</p>
            </div>

            <!-- Statistics Row -->
            <div class="row stats-row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                        <div class="stat-number"><?= $total_certificates ?></div>
                        <div class="stat-label">Total Certificates</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="stat-number"><?= number_format($overall_percentage, 1) ?>%</div>
                        <div class="stat-label">Overall Performance</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                        <div class="stat-number"><?= $total_obtained ?>/<?= $total_marks ?></div>
                        <div class="stat-label">Total Marks</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-number"><?= count(array_filter($certificates, function($cert) { return strtotime($cert['date_issued']) >= strtotime('-30 days'); })) ?></div>
                        <div class="stat-label">Recent (30 days)</div>
                    </div>
                </div>
            </div>

            <!-- Certificates Table -->
            <div class="certificates-table">
                <?php if (count($certificates) === 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h4>No Certificates Found</h4>
                        <p>You haven't earned any certificates yet. Complete exams to get started!</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-book me-2"></i>Subject</th>
                                    <th><i class="fas fa-chart-bar me-2"></i>Marks</th>
                                    <th><i class="fas fa-percentage me-2"></i>Percentage</th>
                                    <th><i class="fas fa-calendar me-2"></i>Date Issued</th>
                                    <th><i class="fas fa-link me-2"></i>Blockchain Hash</th>
                                    <th><i class="fas fa-eye me-2"></i>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($certificates as $cert): 
                                $percentage = ($cert['score'] / $cert['total_marks']) * 100;
                                $percentage_class = $percentage >= 90 ? 'percentage-excellent' : 
                                                  ($percentage >= 80 ? 'percentage-good' : 
                                                  ($percentage >= 70 ? 'percentage-average' : 'percentage-poor'));
                            ?>
                                <tr>
                                    <td>
                                        <div class="subject-name"><?= htmlspecialchars($cert['subject_name']) ?></div>
                                    </td>
                                    <td>
                                        <div class="marks-display"><?= $cert['score'] ?>/<?= $cert['total_marks'] ?></div>
                                    </td>
                                    <td>
                                        <span class="percentage-badge <?= $percentage_class ?>">
                                            <?= number_format($percentage, 1) ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-issued">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?= date('F j, Y', strtotime($cert['date_issued'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cert-hash" title="<?= $cert['certificate_hash'] ?>">
                                            <?= substr($cert['certificate_hash'], 0, 16) ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <a href="../admin/view_certificate.php?id=<?= $cert['id'] ?>" 
                                           class="btn btn-view" 
                                           target="_blank"
                                           title="View Certificate">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 