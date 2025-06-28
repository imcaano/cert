<?php
require_once '../config.php';

// Check if user is logged in (either admin or student)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['student_id'])) {
    header('Location: ../frontend/login.php');
    exit();
}

// Get certificate details
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: certificates.php');
    } else {
        header('Location: ../frontend/student_certificates.php');
    }
    exit();
}

$cert_id = (int)$_GET['id'];

// Check if results table has student_id or user_id
$stmt = $pdo->query("SHOW COLUMNS FROM results LIKE 'student_id'");
$has_student_id = $stmt->fetch();

if ($has_student_id) {
    // Use student_id if it exists
    $stmt = $pdo->prepare("
        SELECT r.*, s.name as student_name, s.email as student_email, s.metamask_address,
               e.title as subject_name, e.total_marks
        FROM results r 
        JOIN students s ON r.student_id = s.id 
        JOIN exams e ON r.exam_id = e.id 
        WHERE r.id = ? AND r.cert_id IS NOT NULL
    ");
} else {
    // Use user_id and join with users table
    $stmt = $pdo->prepare("
        SELECT r.*, u.username as student_name, u.ethereum_address as metamask_address,
               e.title as subject_name, e.total_marks
        FROM results r 
        JOIN users u ON r.user_id = u.id 
        JOIN exams e ON r.exam_id = e.id 
        WHERE r.id = ? AND r.cert_id IS NOT NULL
    ");
}

$stmt->execute([$cert_id]);
$certificate = $stmt->fetch();

if (!$certificate) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: certificates.php');
    } else {
        header('Location: ../frontend/student_certificates.php');
    }
    exit();
}

// If student is viewing, check if it's their own certificate
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    
    // Check if the certificate belongs to this student
    if ($has_student_id) {
        $check_stmt = $pdo->prepare("SELECT id FROM results WHERE id = ? AND student_id = ?");
    } else {
        $check_stmt = $pdo->prepare("SELECT id FROM results WHERE id = ? AND user_id = ?");
    }
    
    $check_stmt->execute([$cert_id, $student_id]);
    if (!$check_stmt->fetch()) {
        header('Location: ../frontend/student_certificates.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?php echo htmlspecialchars($certificate['student_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .certificate-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            margin: 40px auto;
            max-width: 900px;
            position: relative;
        }
        .certificate-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .certificate-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        .certificate-logo {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 48px;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255,255,255,0.3);
        }
        .certificate-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .certificate-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }
        .certificate-body {
            padding: 60px 40px;
            text-align: center;
            position: relative;
        }
        .certificate-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
        }
        .student-name {
            font-size: 3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .certificate-text {
            font-size: 1.3rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 40px;
        }
        .subject-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #dee2e6;
        }
        .subject-name {
            font-size: 1.8rem;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
        }
        .score-info {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 20px 0;
        }
        .score-item {
            text-align: center;
        }
        .score-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .score-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .certificate-id {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 30px 0;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #495057;
            border: 2px dashed #dee2e6;
        }
        .certificate-footer {
            background: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-box {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }
        .signature-line {
            width: 150px;
            height: 2px;
            background: #667eea;
            margin: 10px auto;
        }
        .signature-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .signature-title {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .certificate-date {
            font-size: 1.1rem;
            color: #6c757d;
            margin-top: 20px;
        }
        .verification-info {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .verification-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            color: white;
        }
        @media print {
            body { background: white; }
            .action-buttons { display: none; }
            .certificate-container { 
                box-shadow: none; 
                margin: 0;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="certificates.php" class="btn btn-outline-light me-2">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print me-2"></i>Print
        </button>
    </div>

    <div class="certificate-container">
        <!-- Certificate Header -->
        <div class="certificate-header">
            <div class="certificate-logo">
                <i class="fas fa-certificate"></i>
            </div>
            <h1 class="certificate-title">Certificate of Achievement</h1>
            <p class="certificate-subtitle">CertChain Blockchain Verified</p>
        </div>

        <!-- Certificate Body -->
        <div class="certificate-body">
            <h2 class="student-name"><?php echo htmlspecialchars($certificate['student_name']); ?></h2>
            
            <p class="certificate-text">
                has successfully completed the course requirements and demonstrated exceptional proficiency in
            </p>

            <div class="subject-info">
                <h3 class="subject-name"><?php echo htmlspecialchars($certificate['subject_name']); ?></h3>
                
                <div class="score-info">
                    <div class="score-item">
                        <div class="score-label">Marks Obtained</div>
                        <div class="score-value"><?php echo isset($certificate['score']) ? $certificate['score'] : $certificate['total']; ?></div>
                    </div>
                    <div class="score-item">
                        <div class="score-label">Total Marks</div>
                        <div class="score-value"><?php echo $certificate['total_marks']; ?></div>
                    </div>
                    <div class="score-item">
                        <div class="score-label">Percentage</div>
                        <div class="score-value"><?php 
                            $marks_obtained = isset($certificate['score']) ? $certificate['score'] : $certificate['total'];
                            echo number_format(($marks_obtained / $certificate['total_marks']) * 100, 1); 
                        ?>%</div>
                    </div>
                </div>
            </div>

            <div class="verification-info">
                <div class="verification-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>Blockchain Verified Certificate</h4>
                <p class="mb-0">This certificate is secured on the blockchain and can be verified using the certificate ID below.</p>
            </div>

            <div class="certificate-id">
                Certificate ID: <?php echo htmlspecialchars($certificate['cert_id']); ?>
            </div>

            <p class="certificate-text">
                This certificate is awarded on <?php echo date('F j, Y', strtotime($certificate['date_issued'])); ?> 
                and is valid for lifetime verification.
            </p>
        </div>

        <!-- Certificate Footer -->
        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">CertChain Admin</div>
                    <div class="signature-title">Certificate Authority</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Blockchain System</div>
                    <div class="signature-title">Verification Authority</div>
                </div>
            </div>
            
            <div class="certificate-date">
                Issued on: <?php echo date('F j, Y', strtotime($certificate['date_issued'])); ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 