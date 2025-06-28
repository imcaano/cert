<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Initialize variables
$total_students = 0;
$total_subjects = 0;
$total_certificates = 0;
$pending_certificates = 0;
$recent_students = [];
$recent_certificates = [];

try {
    // Get statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total_students FROM students");
    $total_students = $stmt->fetch()['total_students'];

    $stmt = $pdo->query("SELECT COUNT(*) as total_subjects FROM subjects");
    $total_subjects = $stmt->fetch()['total_subjects'];

    // Check if cert_id column exists before querying certificates
    $stmt = $pdo->query("SHOW COLUMNS FROM results LIKE 'cert_id'");
    $cert_id_exists = $stmt->fetch();
    
    if ($cert_id_exists) {
        $stmt = $pdo->query("SELECT COUNT(*) as total_certificates FROM results WHERE cert_id IS NOT NULL");
        $total_certificates = $stmt->fetch()['total_certificates'];

        $stmt = $pdo->query("SELECT COUNT(*) as pending_certificates FROM results WHERE cert_id IS NULL AND passed = 1");
        $pending_certificates = $stmt->fetch()['pending_certificates'];
    }

    // Get recent students
    $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
    $recent_students = $stmt->fetchAll();

    // Get recent certificates (only if cert_id column exists)
    if ($cert_id_exists) {
        // Check if results table has student_id or user_id
        $stmt = $pdo->query("SHOW COLUMNS FROM results LIKE 'student_id'");
        $has_student_id = $stmt->fetch();
        
        if ($has_student_id) {
            // Use student_id if it exists
            $stmt = $pdo->query("
                SELECT r.*, s.name as student_name, sub.subject_name 
                FROM results r 
                JOIN students s ON r.student_id = s.id 
                JOIN subjects sub ON r.exam_id = sub.id 
                WHERE r.cert_id IS NOT NULL 
                ORDER BY r.date_issued DESC LIMIT 5
            ");
        } else {
            // Use user_id and join with users table
            $stmt = $pdo->query("
                SELECT r.*, u.username as student_name, e.title as subject_name 
                FROM results r 
                JOIN users u ON r.user_id = u.id 
                JOIN exams e ON r.exam_id = e.id 
                WHERE r.cert_id IS NOT NULL 
                ORDER BY r.date_issued DESC LIMIT 5
            ");
        }
        $recent_certificates = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    // Handle database errors gracefully
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CertChain</title>
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .recent-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
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
                        <a class="nav-link active" href="dashboard.php">
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
                            <h2 class="mb-1">Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, Admin!</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0 text-muted"><?php echo date('l, F j, Y'); ?></p>
                        </div>
                    </div>

                    <!-- Database Update Notice -->
                    <?php if (!isset($cert_id_exists) || !$cert_id_exists): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Database Update Required:</strong> Your database needs to be updated to support certificates. 
                            <a href="../update_database.php" class="alert-link">Click here to update now</a>.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Error Message -->
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
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-1"><?php echo $total_students; ?></h3>
                                        <p class="text-muted mb-0">Total Students</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success me-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-1"><?php echo $total_subjects; ?></h3>
                                        <p class="text-muted mb-0">Subjects</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info me-3">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-1"><?php echo $total_certificates; ?></h3>
                                        <p class="text-muted mb-0">Certificates Issued</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning me-3">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-1"><?php echo $pending_certificates; ?></h3>
                                        <p class="text-muted mb-0">Pending Certificates</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="recent-card">
                                <h5 class="mb-3">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    Recent Students
                                </h5>
                                <?php if (empty($recent_students)): ?>
                                    <p class="text-muted">No students registered yet.</p>
                                <?php else: ?>
                                    <?php foreach ($recent_students as $student): ?>
                                        <div class="d-flex align-items-center py-2 border-bottom">
                                            <div class="bg-light rounded-circle p-2 me-3">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($student['name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                            </div>
                                            <small class="text-muted"><?php echo date('M j', strtotime($student['created_at'])); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="recent-card">
                                <h5 class="mb-3">
                                    <i class="fas fa-certificate text-success me-2"></i>
                                    Recent Certificates
                                </h5>
                                <?php if (!isset($cert_id_exists) || !$cert_id_exists): ?>
                                    <p class="text-muted">Database needs to be updated to view certificates.</p>
                                    <a href="../update_database.php" class="btn btn-sm btn-warning">
                                        <i class="fas fa-database me-1"></i>Update Database
                                    </a>
                                <?php elseif (empty($recent_certificates)): ?>
                                    <p class="text-muted">No certificates issued yet.</p>
                                <?php else: ?>
                                    <?php foreach ($recent_certificates as $cert): ?>
                                        <div class="d-flex align-items-center py-2 border-bottom">
                                            <div class="bg-success rounded-circle p-2 me-3">
                                                <i class="fas fa-certificate text-white"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($cert['student_name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($cert['subject_name']); ?></small>
                                            </div>
                                            <small class="text-muted"><?php echo date('M j', strtotime($cert['date_issued'])); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="recent-card">
                                <h5 class="mb-3">
                                    <i class="fas fa-bolt text-warning me-2"></i>
                                    Quick Actions
                                </h5>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <a href="add_student.php" class="btn btn-primary w-100">
                                            <i class="fas fa-plus me-2"></i>Add Student
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="subjects.php" class="btn btn-success w-100">
                                            <i class="fas fa-book me-2"></i>Manage Subjects
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="generate_certificate.php" class="btn btn-info w-100">
                                            <i class="fas fa-certificate me-2"></i>Generate Certificate
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="certificates.php" class="btn btn-warning w-100">
                                            <i class="fas fa-list me-2"></i>View All Certificates
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
