<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle certificate deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("UPDATE results SET cert_id = NULL, certificate_hash = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = "Certificate revoked successfully!";
    } catch (PDOException $e) {
        $error_message = "Error revoking certificate.";
    }
}

// Check if results table has student_id or user_id
$stmt = $pdo->query("SHOW COLUMNS FROM results LIKE 'student_id'");
$has_student_id = $stmt->fetch();

if ($has_student_id) {
    // Use student_id if it exists
    $stmt = $pdo->query("
        SELECT r.*, s.name as student_name, s.email as student_email, s.metamask_address,
               sub.subject_name, sub.marks as total_marks
        FROM results r 
        JOIN students s ON r.student_id = s.id 
        JOIN subjects sub ON r.exam_id = sub.id 
        ORDER BY r.date_issued DESC
    ");
} else {
    // Use user_id and join with users table
    $stmt = $pdo->query("
        SELECT r.*, u.username as student_name, u.ethereum_address as metamask_address,
               e.title as subject_name, e.total_marks
        FROM results r 
        JOIN users u ON r.user_id = u.id 
        JOIN exams e ON r.exam_id = e.id 
        ORDER BY r.date_issued DESC
    ");
}
$certificates = $stmt->fetchAll();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM results WHERE cert_id IS NOT NULL");
$total_certificates = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as pending FROM results WHERE cert_id IS NULL AND passed = 1");
$pending_certificates = $stmt->fetch()['pending'];

// Check if average column exists before calculating average score
$stmt = $pdo->query("SHOW COLUMNS FROM results LIKE 'average'");
$has_average = $stmt->fetch();

if ($has_average) {
    $stmt = $pdo->query("SELECT AVG(average) as avg_score FROM results WHERE cert_id IS NOT NULL");
    $avg_score = $stmt->fetch()['avg_score'];
} else {
    // Calculate average from score if average column doesn't exist
    $stmt = $pdo->query("SELECT AVG(score) as avg_score FROM results WHERE cert_id IS NOT NULL");
    $avg_score = $stmt->fetch()['avg_score'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates Management - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin: 0 auto 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white mb-0">CertChain</h4>
                        <small class="text-white-50">Admin Panel</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="students.php">
                            <i class="fas fa-users me-2"></i> Students
                        </a>
                        <a class="nav-link" href="subjects.php">
                            <i class="fas fa-book me-2"></i> Subjects
                        </a>
                        <a class="nav-link active" href="certificates.php">
                            <i class="fas fa-certificate me-2"></i> Certificates
                        </a>
                        <a class="nav-link" href="generate_certificate.php">
                            <i class="fas fa-plus-circle me-2"></i> Generate Certificate
                        </a>
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">Certificates Management</h2>
                            <p class="text-muted mb-0">Manage and view all certificates</p>
                        </div>
                        <a href="generate_certificate.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Generate New Certificate
                        </a>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $total_certificates; ?></h4>
                                <p class="text-muted mb-0">Total Certificates</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $pending_certificates; ?></h4>
                                <p class="text-muted mb-0">Pending Certificates</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $avg_score ? number_format($avg_score, 1) : '0.0'; ?>%</h4>
                                <p class="text-muted mb-0">Average Score</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h4 class="mb-1"><?php echo count(array_unique(array_column($certificates, 'student_name'))); ?></h4>
                                <p class="text-muted mb-0">Students with Certificates</p>
                            </div>
                        </div>
                    </div>

                    <!-- Certificates Table -->
                    <div class="table-card">
                        <h5 class="mb-3">
                            <i class="fas fa-list text-success me-2"></i>
                            All Certificates & Results
                        </h5>
                        
                        <?php if (empty($certificates)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No certificates found. Generate your first certificate!</p>
                                <a href="generate_certificate.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Generate Certificate
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Subject</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Certificate ID</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($certificates as $cert): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($cert['student_name']); ?></strong>
                                                        <?php if (isset($cert['student_email'])): ?>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($cert['student_email']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($cert['subject_name']); ?></td>
                                                <td>
                                                    <?php if (isset($cert['marks']) && isset($cert['total_marks'])): ?>
                                                        <?php echo $cert['marks']; ?>/<?php echo $cert['total_marks']; ?>
                                                        (<?php echo number_format(($cert['marks'] / $cert['total_marks']) * 100, 1); ?>%)
                                                    <?php elseif (isset($cert['score'])): ?>
                                                        <?php echo $cert['score']; ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($cert['passed']): ?>
                                                        <span class="badge bg-success">Passed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Failed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($cert['cert_id']): ?>
                                                        <code><?php echo htmlspecialchars($cert['cert_id']); ?></code>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not issued</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($cert['date_issued'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($cert['cert_id']): ?>
                                                            <a href="view_certificate.php?id=<?php echo $cert['id']; ?>" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="?delete=<?php echo $cert['id']; ?>" 
                                                               class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('Are you sure you want to revoke this certificate?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="generate_certificate.php?student_id=<?php echo $cert['student_id'] ?? $cert['user_id']; ?>&subject_id=<?php echo $cert['exam_id']; ?>" 
                                                               class="btn btn-sm btn-success">
                                                                <i class="fas fa-certificate me-1"></i>Issue
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 