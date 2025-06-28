<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get student details
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: students.php');
    exit();
}

$student_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: students.php');
    exit();
}

// Get student's certificates
$stmt = $pdo->prepare("
    SELECT r.*, sub.subject_name, sub.marks as total_marks
    FROM results r 
    JOIN subjects sub ON r.exam_id = sub.id 
    WHERE r.student_id = ? 
    ORDER BY r.date_issued DESC
");
$stmt->execute([$student_id]);
$certificates = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Certificates - CertChain Admin</title>
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
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .student-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .certificate-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-issued {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background: #f8d7da;
            color: #721c24;
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
                        <a class="nav-link" href="certificates.php">
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
                            <h2 class="mb-1">Student Certificates</h2>
                            <p class="text-muted mb-0">View certificates for <?php echo htmlspecialchars($student['name']); ?></p>
                        </div>
                        <div>
                            <a href="generate_certificate.php?student_id=<?php echo $student['id']; ?>" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-2"></i>Generate Certificate
                            </a>
                            <a href="students.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Students
                            </a>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="student-info">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    <?php echo htmlspecialchars($student['name']); ?>
                                </h4>
                                <p class="mb-2">
                                    <i class="fas fa-envelope me-2"></i>
                                    <?php echo htmlspecialchars($student['email']); ?>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-wallet me-2"></i>
                                    <code><?php echo htmlspecialchars($student['metamask_address']); ?></code>
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-2">
                                    <strong>Registered:</strong> <?php echo date('F j, Y', strtotime($student['created_at'])); ?>
                                </p>
                                <p class="mb-0">
                                    <strong>Total Certificates:</strong> 
                                    <?php 
                                    $issued_count = 0;
                                    foreach ($certificates as $cert) {
                                        if ($cert['cert_id']) $issued_count++;
                                    }
                                    echo $issued_count;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Certificates Table -->
                    <div class="table-card">
                        <h5 class="mb-3">
                            <i class="fas fa-certificate text-success me-2"></i>
                            All Results & Certificates
                        </h5>
                        
                        <?php if (empty($certificates)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No certificates or results found for this student.</p>
                                <a href="generate_certificate.php?student_id=<?php echo $student['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Generate First Certificate
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
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
                                                    <strong><?php echo htmlspecialchars($cert['subject_name']); ?></strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo $cert['marks']; ?>/<?php echo $cert['total_marks']; ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo number_format($cert['average'], 1); ?>%</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($cert['cert_id']): ?>
                                                        <span class="certificate-status status-issued">
                                                            <i class="fas fa-check me-1"></i>Issued
                                                        </span>
                                                    <?php elseif ($cert['passed']): ?>
                                                        <span class="certificate-status status-pending">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="certificate-status status-failed">
                                                            <i class="fas fa-times me-1"></i>Failed
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($cert['cert_id']): ?>
                                                        <code class="small"><?php echo htmlspecialchars($cert['cert_id']); ?></code>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y', strtotime($cert['date_issued'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <?php if ($cert['cert_id']): ?>
                                                            <a href="view_certificate.php?id=<?php echo $cert['id']; ?>" 
                                                               class="btn btn-outline-info" title="View Certificate">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="certificates.php?delete=<?php echo $cert['id']; ?>" 
                                                               class="btn btn-outline-danger" 
                                                               onclick="return confirm('Are you sure you want to revoke this certificate?')" 
                                                               title="Revoke Certificate">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php elseif ($cert['passed']): ?>
                                                            <a href="generate_certificate.php?student_id=<?php echo $student['id']; ?>&subject_id=<?php echo $cert['exam_id']; ?>" 
                                                               class="btn btn-outline-success" title="Generate Certificate">
                                                                <i class="fas fa-certificate"></i>
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