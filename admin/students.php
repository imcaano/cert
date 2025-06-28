<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle student deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = "Student deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error deleting student. They may have certificates.";
    }
}

// Check if results table has student_id or user_id
$stmt = $pdo->query("SHOW COLUMNS FROM results LIKE 'student_id'");
$has_student_id = $stmt->fetch();

if ($has_student_id) {
    // Use student_id if it exists
    $stmt = $pdo->query("
        SELECT s.*, COUNT(r.id) as certificate_count 
        FROM students s 
        LEFT JOIN results r ON s.id = r.student_id AND r.cert_id IS NOT NULL 
        GROUP BY s.id 
        ORDER BY s.created_at DESC
    ");
} else {
    // Use user_id and join with users table
    $stmt = $pdo->query("
        SELECT s.*, COUNT(r.id) as certificate_count 
        FROM students s 
        LEFT JOIN results r ON s.id = r.user_id AND r.cert_id IS NOT NULL 
        GROUP BY s.id 
        ORDER BY s.created_at DESC
    ");
}
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Management - CertChain</title>
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
                        <a class="nav-link active" href="students.php">
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
                            <h2 class="mb-1">Students Management</h2>
                            <p class="text-muted mb-0">Manage all registered students</p>
            </div>
                        <a href="add_student.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Student
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
                                    <i class="fas fa-users"></i>
                                </div>
                                <h4 class="mb-1"><?php echo count($students); ?></h4>
                                <p class="text-muted mb-0">Total Students</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <h4 class="mb-1"><?php echo array_sum(array_column($students, 'certificate_count')); ?></h4>
                                <p class="text-muted mb-0">Total Certificates</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <h4 class="mb-1"><?php echo count(array_filter($students, function($s) { return $s['certificate_count'] > 0; })); ?></h4>
                                <p class="text-muted mb-0">Students with Certificates</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <h4 class="mb-1"><?php echo count(array_filter($students, function($s) { return $s['certificate_count'] == 0; })); ?></h4>
                                <p class="text-muted mb-0">Students without Certificates</p>
                            </div>
                        </div>
                    </div>

        <!-- Students Table -->
                    <div class="table-card">
                        <h5 class="mb-3">
                            <i class="fas fa-list text-primary me-2"></i>
                            All Students
                        </h5>
                        
            <?php if (empty($students)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No students registered yet. Add your first student!</p>
                                <a href="add_student.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Student
                                </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                            <th>Wallet Address</th>
                                            <th>Certificates</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($student['email']): ?>
                                                        <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>">
                                                            <?php echo htmlspecialchars($student['email']); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not provided</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <code class="small"><?php echo htmlspecialchars($student['metamask_address']); ?></code>
                                                </td>
                                                <td>
                                                    <?php if ($student['certificate_count'] > 0): ?>
                                                        <span class="badge bg-success"><?php echo $student['certificate_count']; ?> certificates</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">No certificates</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($student['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                                        <a href="view_student_certificates.php?id=<?php echo $student['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" 
                                                           class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?delete=<?php echo $student['id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Are you sure you want to delete this student?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
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