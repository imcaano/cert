<?php
require_once 'config.php';

echo "<h2>Setting up exams for subjects...</h2>";

try {
    // Get all subjects
    $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
    $subjects = $stmt->fetchAll();
    
    echo "<p>Found " . count($subjects) . " subjects</p>";
    
    foreach ($subjects as $subject) {
        // Check if exam already exists for this subject
        $stmt = $pdo->prepare("SELECT id FROM exams WHERE title = ?");
        $stmt->execute([$subject['subject_name']]);
        $existing_exam = $stmt->fetch();
        
        if (!$existing_exam) {
            // Create exam for this subject
            $stmt = $pdo->prepare("INSERT INTO exams (title, description, total_marks, duration_minutes) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $subject['subject_name'], 
                'Certificate exam for ' . $subject['subject_name'], 
                $subject['marks'], 
                60
            ]);
            $exam_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>✓ Created exam for {$subject['subject_name']} (ID: {$exam_id})</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Exam for {$subject['subject_name']} already exists (ID: {$existing_exam['id']})</p>";
        }
    }
    
    echo "<h3 style='color: green;'>✓ Setup completed successfully!</h3>";
    echo "<p><a href='admin/generate_certificate.php'>Go to Generate Certificate</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?> 