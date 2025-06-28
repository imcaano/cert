<?php
require_once 'config.php';

$certificate = null;
$error_message = '';
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : 'cert_id';
$search_value = isset($_GET['search_value']) ? trim($_GET['search_value']) : '';

if (!empty($search_value)) {
    if ($search_type === 'cert_id') {
        $stmt = $pdo->prepare("SELECT r.*, u.username as student_name, u.ethereum_address as metamask_address, e.title as subject_name, e.total_marks FROM results r JOIN users u ON r.user_id = u.id JOIN exams e ON r.exam_id = e.id WHERE r.cert_id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT r.*, u.username as student_name, u.ethereum_address as metamask_address, e.title as subject_name, e.total_marks FROM results r JOIN users u ON r.user_id = u.id JOIN exams e ON r.exam_id = e.id WHERE r.certificate_hash = ?");
    }
    $stmt->execute([$search_value]);
    $certificate = $stmt->fetch();
    if (!$certificate) {
        $error_message = 'Certificate not found. Please check your input.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verify-container {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(102,126,234,0.15);
            padding: 40px 30px 30px 30px;
        }
        .verify-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .certificate-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 2px 10px rgba(102,126,234,0.07);
        }
        .certificate-id {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #495057;
            background: #e9ecef;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .not-found {
            color: #dc3545;
            font-weight: 600;
            text-align: center;
            margin-top: 20px;
        }
        .verified-badge {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            border-radius: 8px;
            padding: 6px 18px;
            font-weight: 600;
            margin-bottom: 18px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-title">
            <i class="fas fa-shield-alt me-2"></i>Certificate Verification
        </div>
        <form method="get" action="">
            <div class="mb-3">
                <label for="search_type" class="form-label">Search By</label>
                <select class="form-select" id="search_type" name="search_type" required>
                    <option value="cert_id" <?php if($search_type==='cert_id') echo 'selected'; ?>>Certificate ID</option>
                    <option value="certificate_hash" <?php if($search_type==='certificate_hash') echo 'selected'; ?>>Certificate Hash</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="search_value" class="form-label">Enter Value</label>
                <input type="text" class="form-control" id="search_value" name="search_value" placeholder="e.g. CERT-XXXXXX-2025 or hash..." required value="<?php echo htmlspecialchars($search_value); ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Verify
                </button>
            </div>
        </form>

        <?php if ($error_message): ?>
            <div class="not-found">
                <i class="fas fa-times-circle me-2"></i><?php echo $error_message; ?>
            </div>
        <?php elseif ($certificate): ?>
            <div class="certificate-details mt-4">
                <div class="verified-badge">
                    <i class="fas fa-check-circle me-1"></i>Blockchain Verified
                </div>
                <div class="certificate-id">
                    Certificate ID: <?php echo htmlspecialchars($certificate['cert_id']); ?>
                </div>
                <div><strong>Student:</strong> <?php echo htmlspecialchars($certificate['student_name']); ?></div>
                <div><strong>Subject:</strong> <?php echo htmlspecialchars($certificate['subject_name']); ?></div>
                <div><strong>Marks Obtained:</strong> <?php echo $certificate['score']; ?></div>
                <div><strong>Total Marks:</strong> <?php echo $certificate['total_marks']; ?></div>
                <div><strong>Percentage:</strong> <?php echo number_format(($certificate['score'] / $certificate['total_marks']) * 100, 1); ?>%</div>
                <div><strong>Date Issued:</strong> <?php echo date('F j, Y', strtotime($certificate['date_issued'])); ?></div>
                <div><strong>Blockchain Hash:</strong> <span style="font-family:monospace; color:#667eea;"><?php echo $certificate['certificate_hash']; ?></span></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 