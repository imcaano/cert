<?php
require_once 'config.php';

echo "<h2>CertChain Database Update</h2>";

try {
    // Check what columns exist in results table
    $stmt = $pdo->query("SHOW COLUMNS FROM results");
    $existing_columns = $stmt->fetchAll();
    $column_names = array_column($existing_columns, 'Field');
    
    echo "<p>Checking database structure...</p>";
    echo "<p>Existing columns: " . implode(', ', $column_names) . "</p>";
    
    // Check if cert_id column exists
    $cert_id_exists = in_array('cert_id', $column_names);
    $marks_exists = in_array('marks', $column_names);
    $average_exists = in_array('average', $column_names);
    
    if (!$cert_id_exists) {
        echo "<p>Adding missing columns to results table...</p>";
        
        // Add missing columns
        $pdo->exec("ALTER TABLE results ADD COLUMN cert_id VARCHAR(255) UNIQUE");
        $pdo->exec("ALTER TABLE results ADD COLUMN certificate_hash VARCHAR(255)");
        $pdo->exec("ALTER TABLE results ADD COLUMN total DECIMAL(5,2)");
        $pdo->exec("ALTER TABLE results ADD COLUMN passed TINYINT(1) DEFAULT 0");
        
        echo "<p style='color: green;'>✓ Added missing columns successfully!</p>";
    } else {
        echo "<p style='color: blue;'>✓ Database structure is already up to date.</p>";
    }
    
    // Update existing results to have default values
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM results WHERE total IS NULL");
    $needs_update = $stmt->fetch()['count'];
    
    if ($needs_update > 0) {
        echo "<p>Updating existing results...</p>";
        
        // Use the correct column names based on what exists
        if ($marks_exists) {
            $pdo->exec("UPDATE results SET total = marks WHERE total IS NULL");
        } else {
            // If marks doesn't exist, set total to 0 or a default value
            $pdo->exec("UPDATE results SET total = 0 WHERE total IS NULL");
        }
        
        if ($average_exists) {
            $pdo->exec("UPDATE results SET passed = CASE WHEN average >= 50 THEN 1 ELSE 0 END WHERE passed IS NULL");
        } else {
            // If average doesn't exist, set passed to 0
            $pdo->exec("UPDATE results SET passed = 0 WHERE passed IS NULL");
        }
        
        echo "<p style='color: green;'>✓ Updated {$needs_update} existing results!</p>";
    } else {
        echo "<p style='color: blue;'>✓ All existing results are already updated.</p>";
    }
    
    // Add indexes for better performance
    try {
        $pdo->exec("CREATE INDEX idx_results_cert_id ON results(cert_id)");
        echo "<p style='color: green;'>✓ Added performance indexes!</p>";
    } catch (PDOException $e) {
        echo "<p style='color: blue;'>✓ Indexes already exist.</p>";
    }
    
    echo "<h3 style='color: green;'>Database update completed successfully!</h3>";
    echo "<p><a href='admin/dashboard.php'>Go to Admin Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error updating database: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2 { color: #333; }
p { margin: 10px 0; }
</style> 