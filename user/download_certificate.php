<?php
require_once '../config.php';
require_once '../vendor/fpdf/fpdf.php'; // You need to install FPDF via Composer or manually
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if (!$exam_id) {
    header('Location: dashboard.php');
    exit;
}
// Fetch user info
$user = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$user->execute([$user_id]);
$user = $user->fetch();
// Fetch exam info
$exam = $pdo->prepare('SELECT * FROM exams WHERE id = ?');
$exam->execute([$exam_id]);
$exam = $exam->fetch();
// Fetch result
$result = $pdo->prepare('SELECT * FROM results WHERE user_id = ? AND exam_id = ? ORDER BY date_issued DESC LIMIT 1');
$result->execute([$user_id, $exam_id]);
$result = $result->fetch();
if (!$result || !$result['passed']) {
    header('Location: view_results.php?exam_id=' . $exam_id);
    exit;
}
// Certificate data
$student_name = $user['username'];
$exam_title = $exam['title'];
$score = $result['score'];
$date = $result['date_issued'];
$cert_id = strtoupper(substr(md5($user_id . $exam_id . $date), 0, 10));
// Calculate hash of certificate data
$cert_data = $student_name . '|' . $exam_title . '|' . $score . '|' . $date . '|' . $cert_id;
$blockchain_hash = hash('sha256', $cert_data);
// Store hash in DB if not already stored
if (empty($result['certificate_hash'])) {
    $stmt = $pdo->prepare('UPDATE results SET certificate_hash = ? WHERE id = ?');
    $stmt->execute([$blockchain_hash, $result['id']]);
    // TODO: Interact with blockchain smart contract to store hash
    // Example: call a Node.js script or use web3.php
    // shell_exec("node ../blockchain/store_hash.js $cert_id $student_name $blockchain_hash");
}
// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Certificate of Achievement', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'This is to certify that', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, $student_name, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'has successfully passed the exam', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, $exam_title, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Score: ' . $score, 0, 1, 'C');
$pdf->Cell(0, 10, 'Date: ' . $date, 0, 1, 'C');
$pdf->Cell(0, 10, 'Certificate ID: ' . $cert_id, 0, 1, 'C');
$pdf->Cell(0, 10, 'Blockchain Hash: ' . $blockchain_hash, 0, 1, 'C');
$pdf->Output('D', 'certificate_' . $cert_id . '.pdf');
exit; 