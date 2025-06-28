<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle subject deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = "Subject deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error deleting subject. It may be in use.";
    }
}

// Handle subject addition/editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name']);
    $marks = (int)$_POST['marks'];
    
    if (empty($subject_name) || $marks <= 0) {
        $error_message = "Please fill all fields correctly.";
    } else {
        if (isset($_POST['edit_id'])) {
            // Update existing subject
            $edit_id = (int)$_POST['edit_id'];
            try {
                $stmt = $pdo->prepare("UPDATE subjects SET subject_name = ?, marks = ? WHERE id = ?");
                $stmt->execute([$subject_name, $marks, $edit_id]);
                $success_message = "Subject updated successfully!";
            } catch (PDOException $e) {
                $error_message = "Error updating subject.";
            }
        } else {
            // Add new subject
            try {
                $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, marks) VALUES (?, ?)");
                $stmt->execute([$subject_name, $marks]);
                $success_message = "Subject added successfully!";
            } catch (PDOException $e) {
                $error_message = "Error adding subject.";
            }
        }
    }
}

// Get subjects for editing
$edit_subject = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_subject = $stmt->fetch();
}

// Get all subjects
$stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
$subjects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - CertChain Admin</title>
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
                        <a class="nav-link active" href="subjects.php">
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
                            <h2 class="mb-1">Manage Subjects</h2>
                            <p class="text-muted mb-0">Add, edit, and manage subjects</p>
                        </div>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>

                    <!-- Messages -->
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

                    <div class="row">
                        <!-- Add/Edit Subject Form -->
                        <div class="col-md-4 mb-4">
                            <div class="content-card">
                                <h5 class="mb-3">
                                    <i class="fas fa-<?php echo $edit_subject ? 'edit' : 'plus'; ?> text-primary me-2"></i>
                                    <?php echo $edit_subject ? 'Edit Subject' : 'Add New Subject'; ?>
                                </h5>
                                
                                <form method="POST">
                                    <?php if ($edit_subject): ?>
                                        <input type="hidden" name="edit_id" value="<?php echo $edit_subject['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="subject_name" class="form-label">Subject Name</label>
                                        <input type="text" class="form-control" id="subject_name" name="subject_name" 
                                               value="<?php echo $edit_subject ? htmlspecialchars($edit_subject['subject_name']) : ''; ?>" 
                                               required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="marks" class="form-label">Total Marks</label>
                                        <input type="number" class="form-control" id="marks" name="marks" 
                                               value="<?php echo $edit_subject ? $edit_subject['marks'] : '100'; ?>" 
                                               min="1" max="1000" required>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-<?php echo $edit_subject ? 'save' : 'plus'; ?> me-2"></i>
                                            <?php echo $edit_subject ? 'Update Subject' : 'Add Subject'; ?>
                                        </button>
                                        
                                        <?php if ($edit_subject): ?>
                                            <a href="subjects.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Cancel Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Subjects List -->
                        <div class="col-md-8 mb-4">
                            <div class="table-card">
                                <h5 class="mb-3">
                                    <i class="fas fa-list text-success me-2"></i>
                                    All Subjects
                                </h5>
                                
                                <?php if (empty($subjects)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No subjects found. Add your first subject!</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Subject Name</th>
                                                    <th>Total Marks</th>
                                                    <th>Average</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($subjects as $subject): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($subject['subject_name']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary"><?php echo $subject['marks']; ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $subject['average'] >= 70 ? 'success' : ($subject['average'] >= 50 ? 'warning' : 'danger'); ?>">
                                                                <?php echo number_format($subject['average'], 1); ?>%
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php echo date('M j, Y', strtotime($subject['created_at'])); ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="?edit=<?php echo $subject['id']; ?>" 
                                                                   class="btn btn-outline-primary" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="?delete=<?php echo $subject['id']; ?>" 
                                                                   class="btn btn-outline-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this subject?')" 
                                                                   title="Delete">
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 