<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all students
$stmt = $pdo->query("SELECT * FROM students ORDER BY name");
$students = $stmt->fetchAll();

// Get all subjects (Mathematics, Science, English)
$stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
$subjects = $stmt->fetchAll();

// Handle certificate generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $subject_id = (int)$_POST['subject_id'];
    $marks = (int)$_POST['marks'];
    $total_marks = (int)$_POST['total_marks'];
    
    if ($student_id <= 0 || $subject_id <= 0 || $marks < 0 || $total_marks <= 0) {
        $error_message = "Please fill all fields correctly.";
    } else {
        $average = ($marks / $total_marks) * 100;
        $passed = ($average >= 50) ? 1 : 0;
        
        // Generate unique certificate ID
        $cert_id = 'CERT-' . strtoupper(uniqid()) . '-' . date('Y');
        
        // Generate certificate hash (simplified for demo)
        $certificate_data = $student_id . $subject_id . $marks . $total_marks . date('Y-m-d');
        $certificate_hash = hash('sha256', $certificate_data);
        
        try {
            // Get subject details
            $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
            $stmt->execute([$subject_id]);
            $subject = $stmt->fetch();
            
            if (!$subject) {
                $error_message = "Subject not found.";
            } else {
                // Check if exam exists for this subject, if not create it
                $stmt = $pdo->prepare("SELECT id FROM exams WHERE title = ?");
                $stmt->execute([$subject['subject_name']]);
                $exam = $stmt->fetch();
                
                if (!$exam) {
                    // Create exam for this subject
                    $stmt = $pdo->prepare("INSERT INTO exams (title, description, total_marks, duration_minutes) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$subject['subject_name'], 'Certificate exam for ' . $subject['subject_name'], $total_marks, 60]);
                    $exam_id = $pdo->lastInsertId();
                } else {
                    $exam_id = $exam['id'];
                }
                
                // Check if certificate already exists for this student and exam
                $stmt = $pdo->prepare("SELECT id FROM results WHERE user_id = ? AND exam_id = ? AND cert_id IS NOT NULL");
                $stmt->execute([$student_id, $exam_id]);
                
                if ($stmt->fetch()) {
                    $error_message = "Certificate already exists for this student and subject.";
                } else {
                    // Insert or update result (matching actual database schema)
                    $stmt = $pdo->prepare("
                        INSERT INTO results (user_id, exam_id, score, total, passed, cert_id, certificate_hash, date_issued) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                        score = VALUES(score), 
                        total = VALUES(total), 
                        passed = VALUES(passed), 
                        cert_id = VALUES(cert_id), 
                        certificate_hash = VALUES(certificate_hash), 
                        date_issued = NOW()
                    ");
                    $stmt->execute([$student_id, $exam_id, $average, $total_marks, $passed, $cert_id, $certificate_hash]);
                    
                    $success_message = "Certificate generated successfully! Certificate ID: " . $cert_id;
                    
                    // Clear form data
                    $_POST = array();
                }
            }
        } catch (PDOException $e) {
            $error_message = "Error generating certificate: " . $e->getMessage();
        }
    }
}

// Pre-fill form if student_id and subject_id are provided in URL
$prefill_student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$prefill_subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Certificate - CertChain</title>
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
            background: rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.2);
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .main-content {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            min-height: 100vh;
            border-radius: 20px 0 0 20px;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        }
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-4">
                    <div class="text-center mb-4">
                        <h4 class="text-white mb-0 fw-bold">CertChain</h4>
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
                        <a class="nav-link active" href="generate_certificate.php">
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
                    <!-- Header Section -->
                    <div class="header-section">
                        <h2 class="mb-2 fw-bold">
                            <i class="fas fa-certificate me-3"></i>
                            Generate Certificate
                        </h2>
                        <p class="mb-0 opacity-75">Select student, subject, and enter marks to generate certificate</p>
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

                    <!-- Certificate Generation Form -->
                    <div class="form-card">
                        <h4 class="mb-4 text-center fw-bold">
                            <i class="fas fa-certificate me-2 text-success"></i>
                            Generate Certificate
                        </h4>
                        
                        <form method="POST" action="" id="certificateForm">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="student_id" class="form-label fw-bold">
                                        <i class="fas fa-user me-2 text-primary"></i>Student
                                    </label>
                                    <select class="form-select" id="student_id" name="student_id" required>
                                        <option value="">Select a student</option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?php echo $student['id']; ?>" 
                                                    <?php echo ($prefill_student_id == $student['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($student['name']); ?> 
                                                (<?php echo htmlspecialchars($student['email']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="subject_id" class="form-label fw-bold">
                                        <i class="fas fa-book me-2 text-primary"></i>Subject
                                    </label>
                                    <select class="form-select" id="subject_id" name="subject_id" required>
                                        <option value="">Select a subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['id']; ?>" 
                                                    data-total-marks="<?php echo $subject['marks']; ?>"
                                                    <?php echo ($prefill_subject_id == $subject['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($subject['subject_name']); ?> 
                                                (<?php echo $subject['marks']; ?> marks)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="marks" class="form-label fw-bold">
                                        <i class="fas fa-star me-2 text-warning"></i>Marks Obtained
                                    </label>
                                    <input type="number" class="form-control" id="marks" name="marks" 
                                           value="<?php echo isset($_POST['marks']) ? $_POST['marks'] : ''; ?>" 
                                           min="0" max="1000" required>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="total_marks" class="form-label fw-bold">
                                        <i class="fas fa-chart-bar me-2 text-info"></i>Total Marks
                                    </label>
                                    <input type="number" class="form-control" id="total_marks" name="total_marks" 
                                           value="<?php echo isset($_POST['total_marks']) ? $_POST['total_marks'] : ''; ?>" 
                                           min="1" max="1000" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="alert alert-info border-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Passing Criteria:</strong> Students need to score 50% or higher to pass and be eligible for a certificate.
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-certificate me-2"></i>Generate Certificate
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-calculate total marks when subject is selected
        document.getElementById('subject_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const totalMarks = selectedOption.getAttribute('data-total-marks');
            
            if (totalMarks) {
                document.getElementById('total_marks').value = totalMarks;
            }
        });
    </script>
</body>
</html> 