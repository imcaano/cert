<?php
require_once 'config.php';

echo "<h2>Setting up Subjects</h2>";

try {
    // Check if subjects table exists, if not create it
    $pdo->exec("CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(255) NOT NULL,
        marks INT NOT NULL,
        average DECIMAL(5,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "<p style='color: green;'>✓ Subjects table ready.</p>";
    
    // Insert the 3 required subjects
    $subjects = [
        ['Mathematics', 100],
        ['Science', 100], 
        ['English', 100]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO subjects (subject_name, marks) VALUES (?, ?)");
    
    foreach ($subjects as $subject) {
        $stmt->execute($subject);
        echo "<p style='color: green;'>✓ Subject '{$subject[0]}' with {$subject[1]} marks added.</p>";
    }
    
    // Show current subjects
    $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
    $current_subjects = $stmt->fetchAll();
    
    echo "<h3>Current Subjects:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Subject Name</th><th>Total Marks</th><th>Created</th></tr>";
    
    foreach ($current_subjects as $subject) {
        echo "<tr>";
        echo "<td>" . $subject['id'] . "</td>";
        echo "<td>" . htmlspecialchars($subject['subject_name']) . "</td>";
        echo "<td>" . $subject['marks'] . "</td>";
        echo "<td>" . $subject['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3 style='color: green;'>Subjects setup completed!</h3>";
    echo "<p><a href='admin/generate_certificate.php'>Go to Generate Certificate</a></p>";
    echo "<p><a href='admin/dashboard.php'>Go to Admin Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2, h3 { color: #333; }
table { margin: 20px 0; }
th { background-color: #f0f0f0; padding: 8px; }
td { padding: 8px; }
</style> 